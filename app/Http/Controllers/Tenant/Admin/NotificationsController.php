<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class NotificationsController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index()
    {
        if (! $this->notificationsTableExists()) {
            $notifications = new LengthAwarePaginator(
                [],
                0,
                20,
                null,
                [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]
            );

            $stats = [
                'total' => 0,
                'sent' => 0,
                'scheduled' => 0,
                'messages' => 0,
            ];

            return view('tenant.admin.notifications.index', [
                'notifications' => $notifications,
                'stats' => $stats,
                'notificationsDisabled' => true,
            ]);
        }

        $notifications = Notification::with('creator')
            ->orderByDesc('created_at')
            ->paginate(20);

        $stats = [
            'total' => $notifications->total(),
            'sent' => Notification::whereNotNull('sent_at')->count(),
            'scheduled' => Notification::whereNull('sent_at')->whereNotNull('scheduled_at')->count(),
            'messages' => Notification::count(),
        ];

        return view('tenant.admin.notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        $users = User::select('id', 'name', 'email')->get();
        return view('tenant.admin.notifications.create', compact('users'));
    }

    /**
     * Store a newly created notification.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:general,announcement,alert,reminder',
            'priority' => 'required|in:low,normal,high,urgent',
            'target_audience' => 'nullable|array',
            'specific_recipients' => 'nullable|array',
            'channels' => 'required|array|min:1',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (! $this->notificationsTableExists()) {
            return redirect()->route('tenant.admin.notifications.index')
                ->with('error', __('Notifications feature is not available. Please run the notifications migration.'));
        }

        $notification = Notification::create([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'priority' => $request->priority,
            'target_audience' => $request->target_audience,
            'specific_recipients' => $request->specific_recipients,
            'channels' => $request->channels,
            'scheduled_at' => $request->scheduled_at,
            'created_by' => Auth::id(),
        ]);

        // Send notification immediately if not scheduled
        if (!$request->scheduled_at) {
            $this->sendNotification($notification);
        }

        return redirect()->route('tenant.admin.notifications.index')
            ->with('success', 'Notification created successfully.');
    }

    /**
     * Display the specified notification.
     */
    public function show(Notification $notification)
    {
        $logs = $notification->logs()->with('creator')->orderBy('created_at', 'desc')->get();
        return view('tenant.admin.notifications.show', compact('notification', 'logs'));
    }

    /**
     * Show the form for editing the specified notification.
     */
    public function edit(Notification $notification)
    {
        if ($notification->sent_at) {
            return redirect()->route('tenant.admin.notifications.index')
                ->with('error', 'Cannot edit a sent notification.');
        }

        $users = User::select('id', 'name', 'email')->get();
        return view('tenant.admin.notifications.edit', compact('notification', 'users'));
    }

    /**
     * Update the specified notification.
     */
    public function update(Request $request, Notification $notification)
    {
        if ($notification->sent_at) {
            return redirect()->route('tenant.admin.notifications.index')
                ->with('error', 'Cannot edit a sent notification.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:general,announcement,alert,reminder',
            'priority' => 'required|in:low,normal,high,urgent',
            'target_audience' => 'nullable|array',
            'specific_recipients' => 'nullable|array',
            'channels' => 'required|array|min:1',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $notification->update([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'priority' => $request->priority,
            'target_audience' => $request->target_audience,
            'specific_recipients' => $request->specific_recipients,
            'channels' => $request->channels,
            'scheduled_at' => $request->scheduled_at,
        ]);

        return redirect()->route('tenant.admin.notifications.index')
            ->with('success', 'Notification updated successfully.');
    }

    /**
     * Remove the specified notification.
     */
    public function destroy(Notification $notification)
    {
        if ($notification->sent_at) {
            return redirect()->route('tenant.admin.notifications.index')
                ->with('error', 'Cannot delete a sent notification.');
        }

        $notification->delete();

        return redirect()->route('tenant.admin.notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }

    /**
     * Send a notification immediately.
     */
    public function send(Notification $notification)
    {
        if ($notification->sent_at) {
            return redirect()->back()
                ->with('error', 'Notification has already been sent.');
        }

        $this->sendNotification($notification);

        return redirect()->back()
            ->with('success', 'Notification sent successfully.');
    }

    /**
     * Send notification via configured channels.
     */
    private function sendNotification(Notification $notification)
    {
        $recipients = $this->getRecipients($notification);

        foreach ($notification->channels as $channel) {
            foreach ($recipients as $recipient) {
                if ($this->notificationLogsTableExists()) {
                    NotificationLog::create([
                        'channel' => $channel,
                        'message_type' => 'notification',
                        'to' => $this->getRecipientContact($recipient, $channel),
                        'status' => 'sent',
                        'created_by' => $notification->created_by,
                        'target_type' => get_class($recipient),
                        'target_id' => $recipient->id,
                        'notification_id' => $notification->id,
                        'meta' => [
                            'title' => $notification->title,
                            'message' => $notification->message,
                        ],
                    ]);
                }

                // Here you would integrate with actual notification services
                // (Twilio, Africa's Talking, WhatsApp Cloud API, etc.)
            }
        }

        $notification->update(['sent_at' => now()]);
    }

    /**
     * Get recipients for the notification.
     */
    private function getRecipients(Notification $notification)
    {
        $query = User::query();

        // Filter by target audience
        if ($notification->target_audience) {
            $roles = [];
            if (in_array('admins', $notification->target_audience)) {
                $roles[] = 'admin';
            }
            if (in_array('staff', $notification->target_audience)) {
                $roles[] = 'staff';
            }
            if (in_array('teachers', $notification->target_audience)) {
                $roles[] = 'teacher';
            }
            if (in_array('students', $notification->target_audience)) {
                $roles[] = 'student';
            }
            if (in_array('parents', $notification->target_audience)) {
                $roles[] = 'parent';
            }

            if (!empty($roles)) {
                $query->whereHas('roles', function ($q) use ($roles) {
                    $q->whereIn('name', $roles);
                });
            }
        }

        // Filter by specific recipients
        if ($notification->specific_recipients) {
            $query->orWhereIn('id', $notification->specific_recipients);
        }

        return $query->get();
    }

    /**
     * Get contact information for recipient based on channel.
     */
    private function getRecipientContact(User $user, string $channel)
    {
        switch ($channel) {
            case 'email':
                return $user->email;
            case 'sms':
            case 'whatsapp':
                return $user->phone;
            default:
                return $user->email;
        }
    }

    private function notificationsTableExists(): bool
    {
        static $cache;

        if ($cache !== null) {
            return $cache;
        }

        return $cache = Schema::hasTable('notifications');
    }

    private function notificationLogsTableExists(): bool
    {
        static $cache;

        if ($cache !== null) {
            return $cache;
        }

        return $cache = Schema::hasTable('notification_logs');
    }
}