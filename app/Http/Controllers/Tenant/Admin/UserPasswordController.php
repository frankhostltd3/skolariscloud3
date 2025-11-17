<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SecurityAuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserPasswordController extends Controller
{
    /**
     * Display the user password management page.
     */
    public function index(Request $request): View
    {
        $query = User::query()->with('roles');

        // Search filter
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Active status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deactivated_at');
            } else {
                $query->whereNotNull('deactivated_at');
            }
        }

        $users = $query->orderBy('name', 'asc')->paginate(20);

        $roles = \Spatie\Permission\Models\Role::all();

        return view('tenant.admin.users.password-management', compact('users', 'roles'));
    }

    /**
     * Show the reset password form for a specific user.
     */
    public function show(User $user): View
    {
        // Prevent admins from accessing their own password reset (should use change password)
        if ($user->id === Auth::id()) {
            abort(403, 'You cannot reset your own password here. Use the Change Password page instead.');
        }

        return view('tenant.admin.users.reset-password', compact('user'));
    }

    /**
     * Reset a user's password (Admin only).
     */
    public function reset(Request $request, User $user): RedirectResponse
    {
        $admin = Auth::user();

        // Prevent admins from resetting their own password
        if ($user->id === $admin->id) {
            return back()->with('error', 'You cannot reset your own password. Use the Change Password page instead.');
        }

        $request->validate([
            'password' => ['required', 'confirmed', new \App\Rules\SecurePassword()],
            'reason' => ['required', 'string', 'max:500'],
            'notify_user' => ['boolean'],
        ]);

        // Update password
        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make($request->input('password')),
            'password_changed_at' => now(),
            'password_expires_at' => now()->addDays(90), // Force password change in 90 days
        ]);

        // Log the password reset
        SecurityAuditLog::logEvent(
            SecurityAuditLog::EVENT_PASSWORD_RESET,
            $user->email,
            $user->id,
            "Password reset by admin: {$admin->name} ({$admin->email}). Reason: {$request->reason}",
            [
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'admin_name' => $admin->name,
                'target_user_id' => $user->id,
                'target_user_email' => $user->email,
                'target_user_name' => $user->name,
                'reason' => $request->reason,
                'notify_user' => $request->boolean('notify_user'),
            ],
            SecurityAuditLog::SEVERITY_WARNING
        );

        // Send notification email if requested
        if ($request->boolean('notify_user')) {
            try {
                // TODO: Send email notification to user
                // \Mail::to($user->email)->send(new \App\Mail\PasswordResetByAdmin($user, $admin));
            } catch (\Exception $e) {
                \Log::error('Failed to send password reset notification: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.users.password.index')
            ->with('success', "Password successfully reset for {$user->name}.");
    }

    /**
     * Generate a temporary password and force reset on next login.
     */
    public function generateTemporary(Request $request, User $user): RedirectResponse
    {
        $admin = Auth::user();

        // Prevent admins from generating temp password for themselves
        if ($user->id === $admin->id) {
            return back()->with('error', 'You cannot generate a temporary password for yourself.');
        }

        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        // Generate a secure temporary password
        $tempPassword = $this->generateSecurePassword();

        // Update password and force change on next login
        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make($tempPassword),
            'password_changed_at' => now(),
            'password_expires_at' => now(), // Expired immediately - forces change on next login
            'force_password_change' => true,
        ]);

        // Log the action
        SecurityAuditLog::logEvent(
            SecurityAuditLog::EVENT_PASSWORD_RESET,
            $user->email,
            $user->id,
            "Temporary password generated by admin: {$admin->name} ({$admin->email}). Reason: {$request->reason}",
            [
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'admin_name' => $admin->name,
                'target_user_id' => $user->id,
                'target_user_email' => $user->email,
                'target_user_name' => $user->name,
                'reason' => $request->reason,
                'type' => 'temporary_password',
            ],
            SecurityAuditLog::SEVERITY_WARNING
        );

        // Return with the temporary password (show only once)
        return redirect()->route('admin.users.password.index')
            ->with('success', "Temporary password generated for {$user->name}")
            ->with('temp_password', $tempPassword)
            ->with('temp_user_name', $user->name)
            ->with('temp_user_email', $user->email);
    }

    /**
     * Generate a secure random password.
     */
    private function generateSecurePassword(): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        // Add 4 more random characters
        $allChars = $uppercase . $lowercase . $numbers . $special;
        for ($i = 0; $i < 4; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password
        $password = str_shuffle($password);

        return $password;
    }
}
