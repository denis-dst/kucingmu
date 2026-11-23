<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $notifications = UserNotification::where('user_id', Auth::id())
            ->with('sender')
            ->latest()
            ->paginate(20);

        $unreadCount = UserNotification::where('user_id', Auth::id())
            ->unread()
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a single notification as read and redirect to its destination URL.
     */
    public function markRead(UserNotification $notification)
    {
        if ((int) $notification->user_id !== (int) Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        if ($notification->url) {
            return redirect($notification->url);
        }

        return redirect()->back()->with('success', 'Notifikasi telah ditandai dibaca.');
    }

    /**
     * Mark all unread notifications for the user as read.
     */
    public function markAllRead(Request $request)
    {
        UserNotification::where('user_id', Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
