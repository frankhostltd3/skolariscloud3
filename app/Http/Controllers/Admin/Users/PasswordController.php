<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function index()
    {
        return view('admin.users.password.index');
    }

    public function show(User $user)
    {
        return view('admin.users.password.show', compact('user'));
    }

    public function reset(Request $request, User $user)
    {
        $request->validate([
            'new_password' => ['required', 'confirmed', Password::defaults()],
            'reason' => 'required|string|max:255',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
            'password_changed_at' => now(),
            'password_expires_at' => now()->addDays(90),
        ]);

        // Log the password reset
        \App\Models\SecurityAuditLog::logEvent(
            \App\Models\SecurityAuditLog::EVENT_PASSWORD_RESET,
            $user->email,
            $user->id,
            "Password reset by admin: " . $request->reason,
            ['reset_by' => auth()->id()],
            \App\Models\SecurityAuditLog::SEVERITY_WARNING
        );

        if ($request->notify_user) {
            // TODO: Send notification email
        }

        return back()->with('success', 'Password has been reset successfully.');
    }
}
