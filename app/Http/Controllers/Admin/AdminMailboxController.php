<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\EmailOutbox;
use App\Models\User;
use App\Mail\ContactResponseMail;
use App\Mail\AdminDirectMail;
use App\Mail\SmtpTestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminMailboxController extends Controller
{
    /**
     * Get active SMTP diagnostics info.
     */
    protected function getSmtpInfo(): array
    {
        return [
            'mailer' => config('mail.default', 'smtp'),
            'host' => config('mail.mailers.smtp.host', env('MAIL_HOST', '-')),
            'port' => config('mail.mailers.smtp.port', env('MAIL_PORT', 465)),
            'scheme' => config('mail.mailers.smtp.scheme', env('MAIL_SCHEME', 'smtps')),
            'username' => config('mail.mailers.smtp.username', env('MAIL_USERNAME', '-')),
            'from_address' => config('mail.from.address', env('MAIL_FROM_ADDRESS', 'no-reply@kucingmu.online')),
            'from_name' => config('mail.from.name', config('app.name', 'KucingMu')),
        ];
    }

    /**
     * Display Inbox (Kotak Masuk).
     */
    public function inbox(Request $request)
    {
        $query = ContactMessage::with(['user', 'responder'])->latest();

        // Filter status: all, unread, read, responded
        $statusFilter = $request->get('status', 'all');
        if (in_array($statusFilter, ['unread', 'read', 'responded'])) {
            $query->where('status', $statusFilter);
        }

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::where('status', 'unread')->count(),
            'read' => ContactMessage::where('status', 'read')->count(),
            'responded' => ContactMessage::where('status', 'responded')->count(),
        ];

        $outboxCount = EmailOutbox::count();
        $smtpInfo = $this->getSmtpInfo();

        return view('admin.mail.inbox', compact('messages', 'stats', 'statusFilter', 'outboxCount', 'smtpInfo'));
    }

    /**
     * Display a specific Inbox message.
     */
    public function showInbox(ContactMessage $contact)
    {
        if ($contact->status === 'unread') {
            $contact->update(['status' => 'read']);
        }

        $contact->load(['user', 'responder']);
        $relatedOutbox = EmailOutbox::where('contact_message_id', $contact->id)->latest()->get();
        $smtpInfo = $this->getSmtpInfo();

        return view('admin.mail.inbox-show', compact('contact', 'relatedOutbox', 'smtpInfo'));
    }

    /**
     * Respond to an Inbox message via SMTP and log to Outbox.
     */
    public function replyInbox(Request $request, ContactMessage $contact)
    {
        $request->validate([
            'response' => 'required|string|max:5000',
        ], [
            'response.required' => 'Isi balasan pesan wajib diisi.',
        ]);

        $responseContent = trim($request->response);
        $adminUser = Auth::user();

        $contact->update([
            'admin_response' => $responseContent,
            'responded_by' => $adminUser->id,
            'responded_at' => now(),
            'status' => 'responded',
        ]);

        $status = 'sent';
        $errorMessage = null;

        try {
            Mail::to($contact->email)->send(new ContactResponseMail($contact, $responseContent, $adminUser->name));
        } catch (\Throwable $e) {
            $status = 'failed';
            $errorMessage = $e->getMessage();
            Log::error("Gagal mengirim email balasan ke {$contact->email}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }

        // Log to Outbox
        EmailOutbox::logOutbox([
            'sender_id' => $adminUser->id,
            'contact_message_id' => $contact->id,
            'recipient_email' => $contact->email,
            'recipient_name' => $contact->name,
            'subject' => "Re: {$contact->subject}",
            'body' => $responseContent,
            'mail_type' => 'contact_reply',
            'mailer' => config('mail.default', 'smtp'),
            'status' => $status,
            'error_message' => $errorMessage,
        ]);

        if ($status === 'sent') {
            return redirect()->back()->with('success', "Balasan berhasil dikirim melalui SMTP ke {$contact->email} dan dicatat di Kotak Keluar (Outbox).");
        } else {
            return redirect()->back()->with('warning', "Balasan disimpan di sistem, namun pengiriman email ke {$contact->email} gagal: {$errorMessage}. Anda dapat mencoba 'Kirim Ulang' di halaman Kotak Keluar.");
        }
    }

    /**
     * Mark an Inbox message as read or unread.
     */
    public function markInboxStatus(Request $request, ContactMessage $contact)
    {
        $targetStatus = $request->input('status');
        if (in_array($targetStatus, ['unread', 'read'])) {
            $contact->update(['status' => $targetStatus]);
            return redirect()->back()->with('success', 'Status pesan berhasil diubah menjadi: ' . $contact->status_label);
        }

        return redirect()->back()->with('error', 'Status tidak valid.');
    }

    /**
     * Delete an Inbox message.
     */
    public function destroyInbox(ContactMessage $contact)
    {
        $subject = $contact->subject;
        $contact->delete();

        return redirect()->route('admin.mail.inbox')->with('success', "Pesan \"{$subject}\" berhasil dihapus dari Kotak Masuk.");
    }

    /**
     * Display Outbox (Kotak Keluar).
     */
    public function outbox(Request $request)
    {
        $query = EmailOutbox::with(['sender', 'contactMessage'])->latest();

        // Filter status: all, sent, failed
        $statusFilter = $request->get('status', 'all');
        if (in_array($statusFilter, ['sent', 'failed'])) {
            $query->where('status', $statusFilter);
        }

        // Filter mail_type: all, direct_compose, contact_reply, test_smtp, registration_notice
        $typeFilter = $request->get('type', 'all');
        if (in_array($typeFilter, ['direct_compose', 'contact_reply', 'test_smtp', 'registration_notice'])) {
            $query->where('mail_type', $typeFilter);
        }

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('recipient_email', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $outboxes = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => EmailOutbox::count(),
            'sent' => EmailOutbox::where('status', 'sent')->count(),
            'failed' => EmailOutbox::where('status', 'failed')->count(),
            'direct' => EmailOutbox::where('mail_type', 'direct_compose')->count(),
            'replies' => EmailOutbox::where('mail_type', 'contact_reply')->count(),
        ];

        $unreadInboxCount = ContactMessage::where('status', 'unread')->count();
        $smtpInfo = $this->getSmtpInfo();

        // Registered users for quick autocomplete in compose modal
        $usersList = User::select('id', 'name', 'email', 'role')->orderBy('name')->take(100)->get();

        return view('admin.mail.outbox', compact('outboxes', 'stats', 'statusFilter', 'typeFilter', 'unreadInboxCount', 'smtpInfo', 'usersList'));
    }

    /**
     * Display a specific Outbox email log.
     */
    public function showOutbox(EmailOutbox $outbox)
    {
        $outbox->load(['sender', 'contactMessage']);
        $smtpInfo = $this->getSmtpInfo();

        return view('admin.mail.outbox-show', compact('outbox', 'smtpInfo'));
    }

    /**
     * Compose and send a new email via SMTP, recording in Outbox.
     */
    public function compose(Request $request)
    {
        $request->validate([
            'recipient_email' => 'required|email|max:255',
            'recipient_name' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
        ], [
            'recipient_email.required' => 'Email penerima wajib diisi.',
            'recipient_email.email' => 'Format email penerima tidak valid.',
            'subject.required' => 'Subjek email wajib diisi.',
            'body.required' => 'Isi pesan email wajib diisi.',
        ]);

        $recipientEmail = trim($request->recipient_email);
        $recipientName = trim($request->recipient_name ?: '');
        $subject = trim($request->subject);
        $body = trim($request->body);
        $adminUser = Auth::user();

        $status = 'sent';
        $errorMessage = null;

        try {
            Mail::to($recipientEmail)->send(new AdminDirectMail($subject, $body, $recipientName, $adminUser->name));
        } catch (\Throwable $e) {
            $status = 'failed';
            $errorMessage = $e->getMessage();
            Log::error("Gagal mengirim email langsung ke {$recipientEmail}: " . $e->getMessage());
        }

        $outboxRecord = EmailOutbox::logOutbox([
            'sender_id' => $adminUser->id,
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'subject' => $subject,
            'body' => $body,
            'mail_type' => 'direct_compose',
            'mailer' => config('mail.default', 'smtp'),
            'status' => $status,
            'error_message' => $errorMessage,
        ]);

        if ($status === 'sent') {
            return redirect()->route('admin.mail.outbox')->with('success', "Email berhasil dikirim melalui SMTP ke {$recipientEmail} dan dicatat di Kotak Keluar.");
        } else {
            return redirect()->route('admin.mail.outbox')->with('warning', "Email gagal dikirim ke {$recipientEmail}: {$errorMessage}. Log telah dicatat dan Anda dapat mencoba Kirim Ulang.");
        }
    }

    /**
     * Resend an email that failed or needs to be resent.
     */
    public function resendOutbox(EmailOutbox $outbox)
    {
        $adminUser = Auth::user();
        $status = 'sent';
        $errorMessage = null;

        try {
            if ($outbox->mail_type === 'contact_reply' && $outbox->contactMessage) {
                Mail::to($outbox->recipient_email)->send(new ContactResponseMail($outbox->contactMessage, $outbox->body, $adminUser->name));
            } else {
                Mail::to($outbox->recipient_email)->send(new AdminDirectMail($outbox->subject, $outbox->body, $outbox->recipient_name ?: '', $adminUser->name));
            }
        } catch (\Throwable $e) {
            $status = 'failed';
            $errorMessage = $e->getMessage();
            Log::error("Gagal mengirim ulang email ID {$outbox->id} ke {$outbox->recipient_email}: " . $e->getMessage());
        }

        $outbox->update([
            'status' => $status,
            'error_message' => $errorMessage,
            'sent_at' => $status === 'sent' ? now() : $outbox->sent_at,
        ]);

        if ($status === 'sent') {
            return redirect()->back()->with('success', "Email berhasil dikirim ulang ke {$outbox->recipient_email} melalui SMTP.");
        } else {
            return redirect()->back()->with('error', "Gagal mengirim ulang email: {$errorMessage}");
        }
    }

    /**
     * Test active SMTP configuration by sending a diagnostic test email.
     */
    public function testSmtp(Request $request)
    {
        $adminUser = Auth::user();
        $testEmail = trim($request->input('test_email', $adminUser->email));

        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Format alamat email uji coba tidak valid.');
        }

        $smtpDetails = $this->getSmtpInfo();
        $status = 'sent';
        $errorMessage = null;

        try {
            Mail::to($testEmail)->send(new SmtpTestMail($adminUser->name, $smtpDetails));
        } catch (\Throwable $e) {
            $status = 'failed';
            $errorMessage = $e->getMessage();
            Log::error("Uji coba SMTP gagal ke {$testEmail}: " . $e->getMessage());
        }

        // Log to Outbox
        EmailOutbox::logOutbox([
            'sender_id' => $adminUser->id,
            'recipient_email' => $testEmail,
            'recipient_name' => $adminUser->name,
            'subject' => '✅ Uji Coba Konfigurasi SMTP Berhasil',
            'body' => "Pesan uji coba diagnostik server email SMTP ({$smtpDetails['host']}:{$smtpDetails['port']}).",
            'mail_type' => 'test_smtp',
            'mailer' => config('mail.default', 'smtp'),
            'status' => $status,
            'error_message' => $errorMessage,
        ]);

        if ($status === 'sent') {
            return redirect()->back()->with('success', "✅ Uji coba SMTP BERHASIL! Email diagnostik terkirim ke {$testEmail}. Server SMTP ({$smtpDetails['host']}:{$smtpDetails['port']}) berfungsi normal.");
        } else {
            return redirect()->back()->with('error', "❌ Uji coba SMTP GAGAL ke {$testEmail}. Error koneksi: {$errorMessage}");
        }
    }

    /**
     * Delete an Outbox log entry.
     */
    public function destroyOutbox(EmailOutbox $outbox)
    {
        $subject = $outbox->subject;
        $outbox->delete();

        return redirect()->route('admin.mail.outbox')->with('success', "Log email keluar \"{$subject}\" berhasil dihapus.");
    }
}
