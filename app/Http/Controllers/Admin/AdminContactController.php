<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Mail\ContactResponseMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminContactController extends Controller
{
    /**
     * Display a listing of contact messages.
     */
    public function index(Request $request)
    {
        $query = ContactMessage::with(['user', 'responder'])->latest();

        // Filter by status
        $statusFilter = $request->get('status', 'all');
        if (in_array($statusFilter, ['unread', 'read', 'responded'])) {
            $query->where('status', $statusFilter);
        }

        // Search query
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

        return view('admin.contacts.index', compact('messages', 'stats', 'statusFilter'));
    }

    /**
     * Display the specified contact message and mark as read.
     */
    public function show(ContactMessage $contact)
    {
        if ($contact->status === 'unread') {
            $contact->update(['status' => 'read']);
        }

        $contact->load(['user', 'responder']);

        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Respond to a contact message and email the sender.
     */
    public function respond(Request $request, ContactMessage $contact)
    {
        $request->validate([
            'response' => 'required|string|max:5000',
        ], [
            'response.required' => 'Isi balasan / respon wajib diisi.',
        ]);

        $responseContent = trim($request->response);
        $adminUser = Auth::user();

        $contact->update([
            'admin_response' => $responseContent,
            'responded_by' => $adminUser->id,
            'responded_at' => now(),
            'status' => 'responded',
        ]);

        // Attempt to send email reply
        $mailSent = false;
        try {
            Mail::to($contact->email)->send(new ContactResponseMail($contact, $responseContent, $adminUser->name));
            $mailSent = true;
        } catch (\Throwable $e) {
            Log::warning("Gagal mengirim email balasan kontak ke {$contact->email}: " . $e->getMessage());
        }

        $feedback = $mailSent
            ? 'Respon berhasil disimpan dan email notifikasi balasan telah terkirim ke ' . $contact->email . '.'
            : 'Respon berhasil disimpan di sistem (namun pengiriman email ke ' . $contact->email . ' mengalami kendala koneksi mail server).';

        return redirect()->back()->with('success', $feedback);
    }

    /**
     * Toggle read/unread status.
     */
    public function markStatus(Request $request, ContactMessage $contact)
    {
        $targetStatus = $request->input('status');
        if (in_array($targetStatus, ['unread', 'read'])) {
            $contact->update(['status' => $targetStatus]);
            return redirect()->back()->with('success', 'Status pesan berhasil diubah menjadi: ' . $contact->status_label);
        }

        return redirect()->back()->with('error', 'Status tidak valid.');
    }

    /**
     * Remove the specified contact message from storage.
     */
    public function destroy(ContactMessage $contact)
    {
        $subject = $contact->subject;
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', "Pesan \"{$subject}\" berhasil dihapus.");
    }
}
