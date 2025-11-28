<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Notifications\GenericTenantNotification;
use Illuminate\Support\Facades\Notification;

class NotificationsController extends Controller
{
    public function index()
    {
        // In a real app, this would list sent notifications or received notifications
        // For now, we'll just show a placeholder or redirect to create if empty
        return view('admin.notifications.index');
    }

    public function create()
    {
        $roles = Role::all();
        // We might want to pass users for the select2/autocomplete field if the list isn't too huge
        // For large user bases, an AJAX search endpoint is better.
        // For now, let's pass all users but be mindful of performance.
        $users = User::select('id', 'name', 'email', 'user_type')->get();

        return view('admin.notifications.create', compact('roles', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,danger',
            'recipients' => 'required|in:all,role,users',
            'role_id' => 'required_if:recipients,role|nullable|exists:roles,id',
            'user_ids' => 'required_if:recipients,users|nullable|array',
            'user_ids.*' => 'exists:users,id',
            'channels' => 'required|array',
            'channels.*' => 'in:database,mail', // SMS requires setup
        ]);

        $users = collect();

        if ($request->recipients === 'all') {
            $users = User::all();
        } elseif ($request->recipients === 'role') {
            if (!$request->role_id) {
                return back()->withErrors(['role_id' => 'Please select a role.'])->withInput();
            }
            $users = User::role($request->role_id)->get();
        } elseif ($request->recipients === 'users') {
            if (empty($request->user_ids)) {
                return back()->withErrors(['user_ids' => 'Please select at least one user.'])->withInput();
            }
            $users = User::whereIn('id', $request->user_ids)->get();
        }

        if ($users->isEmpty()) {
            return back()->with('error', 'No recipients found for the selected criteria.')->withInput();
        }

        // Send notification
        Notification::send($users, new GenericTenantNotification(
            $request->title,
            $request->message,
            $request->type,
            $request->channels
        ));

        return redirect()->route('admin.notifications.index')->with('success', 'Notification sent successfully to ' . $users->count() . ' users.');
    }
}
