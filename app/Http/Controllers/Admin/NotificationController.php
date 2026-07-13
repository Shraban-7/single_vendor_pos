<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function pollNotifications()
    {
        $unreadCount = Notification::where('user_id', auth()->id())->unread()->count();
        $latestNotifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'time' => $notif->created_at->diffForHumans(),
                    'color' => $notif->type->color(),
                    'icon' => $notif->type->icon(),
                    'action_url' => $notif->action_url ? route('admin.notifications.read-redirect', $notif->id) : '#',
                    'is_unread' => is_null($notif->read_at),
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $latestNotifications,
        ]);
    }

    public function readAndRedirect(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return redirect()->route('admin.notifications.index');
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.'
        ]);
    }
    
    public function readAll()
    {
        Notification::where('user_id', auth()->id())
            ->unread()
            ->update(['read_at' => now()]);

        return redirect()
            ->back()
            ->with('success', 'All notifications marked as read.');
    }
}
