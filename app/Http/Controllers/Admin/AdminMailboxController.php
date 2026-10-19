<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\EmailOutbox;
use App\Models\User;
use App\Models\Cat;
use App\Models\MasterWilayah;
use App\Models\Event;
use App\Mail\ContactResponseMail;
use App\Mail\AdminDirectMail;
use App\Mail\SmtpTestMail;
use App\Mail\KtamUpdateNotificationMail;
use App\Mail\BroadcastAnnouncementMail;
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

        // Filter mail_type: all, direct_compose, contact_reply, ktam_broadcast, event_broadcast, general_broadcast, broadcasts, test_smtp, registration_notice
        $typeFilter = $request->get('type', 'all');
        if ($typeFilter === 'broadcasts' || $typeFilter === 'ktam_broadcast') {
            $query->whereIn('mail_type', ['ktam_broadcast', 'event_broadcast', 'general_broadcast']);
        } elseif (in_array($typeFilter, ['direct_compose', 'contact_reply', 'event_broadcast', 'general_broadcast', 'test_smtp', 'registration_notice'])) {
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
            'broadcasts' => EmailOutbox::whereIn('mail_type', ['ktam_broadcast', 'event_broadcast', 'general_broadcast'])->count(),
        ];

        $unreadInboxCount = ContactMessage::where('status', 'unread')->count();
        $smtpInfo = $this->getSmtpInfo();

        // Registered users for quick autocomplete in compose modal
        $usersList = User::select('id', 'name', 'email', 'role')->orderBy('name')->take(100)->get();

        // Count members based on KTAKuMu ownership
        $ktamIssuedCatsCount = Cat::whereNotNull('unique_code')->where('unique_code', '!=', '')->count();
        $ktamMembersCount = User::whereHas('cats', function ($q) {
            $q->whereNotNull('unique_code')->where('unique_code', '!=', '');
        })->count();
        $nonKtamMembersCount = User::whereDoesntHave('cats', function ($q) {
            $q->whereNotNull('unique_code')->where('unique_code', '!=', '');
        })->count();
        $totalMembersCount = User::count();

        // Active events list for event broadcast selection
        $activeEvents = Event::where('status', 'active')->orderBy('date', 'asc')->get();

        // Master Wilayah list for broadcast filter
        $wilayahList = MasterWilayah::where('is_active', true)->orderBy('nama')->get();

        return view('admin.mail.outbox', compact(
            'outboxes', 
            'stats', 
            'statusFilter', 
            'typeFilter', 
            'unreadInboxCount', 
            'smtpInfo', 
            'usersList',
            'ktamIssuedCatsCount',
            'ktamMembersCount',
            'nonKtamMembersCount',
            'totalMembersCount',
            'activeEvents',
            'wilayahList'
        ));
    }

    /**
     * Broadcast notification or event announcement to members.
     */
    public function broadcastKtamUpdate(Request $request)
    {
        $request->validate([
            'target_audience' => 'nullable|in:all,ktam_issued,ktam_not_issued',
            'broadcast_type' => 'nullable|in:event_announcement,general_announcement,ktam_update',
            'event_id' => 'nullable|exists:events,id',
            'subject' => 'required|string|max:255',
            'message' => 'nullable|string|max:10000',
            'custom_note' => 'nullable|string|max:10000',
            'wilayah_code' => 'nullable|string|max:20',
            'cta_text' => 'nullable|string|max:100',
            'cta_url' => 'nullable|string|max:500',
        ]);

        @set_time_limit(300); // Extended execution time if server permits

        $adminUser = Auth::user();
        $adminId = $adminUser?->id;
        $adminName = $adminUser?->name ?: 'Admin KucingMu';

        $targetAudience = $request->input('target_audience', 'all');
        $broadcastType = $request->input('broadcast_type', 'event_announcement');
        $wilayahFilter = $request->input('wilayah_code');
        $eventId = $request->input('event_id');
        $event = $eventId ? Event::find($eventId) : null;

        $subjectInput = trim($request->input('subject', ''));
        $messageBody = trim($request->input('message') ?: $request->input('custom_note', ''));
        $ctaText = trim($request->input('cta_text', ''));
        $ctaUrl = trim($request->input('cta_url', ''));

        // Query users based on target_audience
        $usersQuery = User::whereNotNull('email')->where('email', '!=', '');

        if ($targetAudience === 'ktam_issued') {
            $usersQuery->whereHas('cats', function ($q) use ($wilayahFilter) {
                $q->whereNotNull('unique_code')->where('unique_code', '!=', '');
                if (!empty($wilayahFilter) && $wilayahFilter !== 'all') {
                    $q->where('wilayah_code', $wilayahFilter);
                }
            });
        } elseif ($targetAudience === 'ktam_not_issued') {
            $usersQuery->whereDoesntHave('cats', function ($q) {
                $q->whereNotNull('unique_code')->where('unique_code', '!=', '');
            });
            if (!empty($wilayahFilter) && $wilayahFilter !== 'all') {
                $usersQuery->where(function ($uq) use ($wilayahFilter) {
                    $uq->whereHas('cats', function ($cq) use ($wilayahFilter) {
                        $cq->where('wilayah_code', $wilayahFilter);
                    })->orWhereDoesntHave('cats');
                });
            }
        } else { // 'all'
            if (!empty($wilayahFilter) && $wilayahFilter !== 'all') {
                $usersQuery->whereHas('cats', function ($q) use ($wilayahFilter) {
                    $q->where('wilayah_code', $wilayahFilter);
                });
            }
        }

        $recipients = $usersQuery->with(['cats' => function ($q) {
            $q->whereNotNull('unique_code')->where('unique_code', '!=', '');
        }])->get();

        if ($recipients->isEmpty()) {
            return redirect()->back()->with('warning', 'Tidak ditemukan member penerima yang sesuai dengan kriteria target dan filter wilayah terpilih.');
        }

        $sentCount = 0;
        $failedCount = 0;
        $skippedCount = 0;
        $circuitBroken = false;
        $criticalErrorMessage = null;
        $timedOut = false;

        $mailType = match ($broadcastType) {
            'event_announcement' => 'event_broadcast',
            'ktam_update' => 'ktam_broadcast',
            default => 'general_broadcast',
        };

        // Calculate a safe time budget to prevent HTTP 500 / 504 timeouts from web server / PHP-FPM
        $maxExecutionTime = (int) @ini_get('max_execution_time');
        $timeBudgetSeconds = ($maxExecutionTime > 5 && $maxExecutionTime <= 60) ? ($maxExecutionTime - 5) : 25;
        $startTime = microtime(true);

        foreach ($recipients as $index => $owner) {
            // Guard against request timeout if sending in bulk
            $elapsed = microtime(true) - $startTime;
            if ($elapsed >= $timeBudgetSeconds) {
                $timedOut = true;
                // Mark remaining recipients as pending in Outbox so admin can resume
                $remainingRecipients = $recipients->slice($index);
                foreach ($remainingRecipients as $remOwner) {
                    if (empty($remOwner->email)) continue;
                    EmailOutbox::logOutbox([
                        'sender_id' => $adminId,
                        'recipient_email' => $remOwner->email,
                        'recipient_name' => $remOwner->name,
                        'subject' => $subjectInput ?: 'Pengumuman Komunitas KucingMu',
                        'body' => "Pesan tertunda karena batas waktu eksekusi web tercapai (Time limit). Anda dapat mengirim ulang melalui Kotak Keluar.",
                        'mail_type' => $mailType,
                        'mailer' => config('mail.default', 'smtp'),
                        'status' => 'failed',
                        'error_message' => 'Batas waktu eksekusi web tercapai. Gunakan tombol Kirim Ulang di Kotak Keluar.',
                    ]);
                    $failedCount++;
                }
                break;
            }

            if (empty($owner->email) || !filter_var($owner->email, FILTER_VALIDATE_EMAIL)) {
                $skippedCount++;
                continue;
            }

            // If circuit breaker is tripped due to SMTP authentication failure or network down
            if ($circuitBroken) {
                EmailOutbox::logOutbox([
                    'sender_id' => $adminId,
                    'recipient_email' => $owner->email,
                    'recipient_name' => $owner->name,
                    'subject' => $subjectInput ?: 'Pengumuman Komunitas KucingMu',
                    'body' => "Pengiriman dibatalkan otomatis karena server SMTP menolak koneksi atau autentikasi:\n{$criticalErrorMessage}",
                    'mail_type' => $mailType,
                    'mailer' => config('mail.default', 'smtp'),
                    'status' => 'failed',
                    'error_message' => $criticalErrorMessage,
                ]);
                $failedCount++;
                continue;
            }

            $userCats = $owner->cats;
            $catNames = $userCats->isNotEmpty() ? $userCats->pluck('name')->implode(', ') : '';
            $catCodes = $userCats->isNotEmpty() ? $userCats->pluck('unique_code')->implode(', ') : '';

            $subject = $subjectInput ?: match ($broadcastType) {
                'event_announcement' => $event ? "[KucingMu] Undangan Agenda: {$event->title}" : "[KucingMu] Pengumuman Agenda Kegiatan",
                'ktam_update' => $catNames ? "[KucingMu] Pemberitahuan Penyesuaian KTAKuMu ({$catNames})" : "[KucingMu] Pemberitahuan Penyesuaian KTAKuMu",
                default => "[KucingMu] Pengumuman Resmi Komunitas",
            };

            $status = 'sent';
            $errorMessage = null;

            try {
                if ($broadcastType === 'ktam_update' && $userCats->isNotEmpty()) {
                    Mail::to($owner->email)->send(new KtamUpdateNotificationMail($owner, $userCats, $messageBody, $adminName));
                } else {
                    Mail::to($owner->email)->send(new BroadcastAnnouncementMail(
                        $owner,
                        $subject,
                        $messageBody,
                        $event,
                        $broadcastType,
                        $ctaText,
                        $ctaUrl,
                        $adminName
                    ));
                }
                $sentCount++;
            } catch (\Throwable $e) {
                $status = 'failed';
                $errorMessage = $e->getMessage();
                $failedCount++;
                Log::error("Gagal broadcast email ke {$owner->email}: " . $e->getMessage());

                // Detect critical connection or authentication failures to trip circuit breaker
                $errMsgLower = strtolower($errorMessage);
                if (
                    str_contains($errorMessage, '535') ||
                    str_contains($errMsgLower, 'incorrect authentication data') ||
                    str_contains($errMsgLower, 'failed to authenticate') ||
                    str_contains($errMsgLower, 'connection refused') ||
                    str_contains($errMsgLower, 'relay access denied')
                ) {
                    $circuitBroken = true;
                    $criticalErrorMessage = "Server SMTP menolak autentikasi atau koneksi: {$errorMessage}";
                }
            }

            // Body summary for Outbox log
            $bodyLog = "Penerima: {$owner->name} ({$owner->email})\n";
            $bodyLog .= "Target: " . match ($targetAudience) {
                'ktam_issued' => 'Member Pemilik KTAKuMu',
                'ktam_not_issued' => 'Member Belum Memiliki KTAKuMu',
                default => 'Semua Member',
            } . "\n\n";

            if ($event) {
                $bodyLog .= "Agenda Kegiatan Terlampir: {$event->title}\n";
                $bodyLog .= "Waktu: " . ($event->date ? $event->date->format('d M Y') : '-') . " | Lokasi: {$event->location}\n\n";
            }

            if (!empty($messageBody)) {
                $bodyLog .= "Isi Pesan:\n{$messageBody}\n\n";
            }

            if ($ctaUrl) {
                $bodyLog .= "Tautan Aksi (CTA): {$ctaUrl}\n";
            }

            // Record into EmailOutbox safely
            EmailOutbox::logOutbox([
                'sender_id' => $adminId,
                'recipient_email' => $owner->email,
                'recipient_name' => $owner->name,
                'subject' => $subject,
                'body' => $bodyLog,
                'mail_type' => $mailType,
                'mailer' => config('mail.default', 'smtp'),
                'status' => $status,
                'error_message' => $errorMessage,
            ]);

            // Slight throttle to prevent flooding mail server
            usleep(30000); // 30ms
        }

        $targetLabel = match ($targetAudience) {
            'ktam_issued' => 'member pemilik KTAKuMu',
            'ktam_not_issued' => 'member yang belum memiliki KTAKuMu',
            default => 'seluruh member terpilih',
        };

        if ($circuitBroken) {
            return redirect()->route('admin.mail.outbox', ['type' => $mailType])
                ->with('error', "❌ Pengiriman broadcast dihentikan karena server SMTP menolak autentikasi atau koneksi: {$criticalErrorMessage}. Harap periksa kredensial MAIL_USERNAME dan MAIL_PASSWORD di konfigurasi server.");
        }

        if ($timedOut) {
            return redirect()->route('admin.mail.outbox', ['type' => $mailType])
                ->with('warning', "⚠️ Batas waktu eksekusi web tercapai demi keamanan server: {$sentCount} email terkirim via SMTP, {$failedCount} tertunda ke {$targetLabel}. Seluruh log dicatat di Kotak Keluar dan Anda dapat melanjutkan pengiriman menggunakan tombol 'Kirim Ulang'.");
        }

        if ($failedCount === 0) {
            return redirect()->route('admin.mail.outbox', ['type' => $mailType])
                ->with('success', "✅ Broadcast berhasil! {$sentCount} email telah terkirim via SMTP ke {$targetLabel} dan dicatat di Kotak Keluar.");
        } else {
            return redirect()->route('admin.mail.outbox', ['type' => $mailType])
                ->with('warning', "Broadcast selesai: {$sentCount} berhasil terkirim, {$failedCount} gagal ke {$targetLabel}. Anda dapat melihat detail log dan melakukan Kirim Ulang pada email yang gagal.");
        }
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
