<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    /**
     * Display all notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            return redirect()->route('tenant.student.dashboard')
                ->with('error', 'Student record not found.');
        }

        // Get all notifications for this user
        if ($request->filled('filter') && $request->filter == 'unread') {
            $notifications = $user->unreadNotifications()->paginate(20);
        } elseif ($request->filled('filter') && $request->filter == 'read') {
            $notifications = $user->readNotifications()->paginate(20);
        } else {
            $notifications = $user->notifications()->paginate(20);
        }

        // Get statistics
        $statistics = [
            'total' => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
            'today' => $user->notifications()->whereDate('created_at', today())->count(),
        ];

        return view('tenant.student.notifications.index', compact('notifications', 'statistics', 'student'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read!');
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->delete();
        }

        return redirect()->back()->with('success', 'Notification deleted successfully!');
    }

    /**
     * Get unread count (AJAX)
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $user->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }
}