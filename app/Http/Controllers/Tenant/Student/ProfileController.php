<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\SecurityAuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $student = $user->student; // Assuming user has student relationship

        return view('tenant.student.profile', compact('user', 'student'));
    }

    /**
     * Show the change password page.
     */
    public function changePassword(): View
    {
        return view('tenant.profile.change-password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', new \App\Rules\SecurePassword()],
        ]);

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->with('password_error', 'Current password is incorrect.');
        }

        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make($request->input('password')),
            'password_changed_at' => now(),
        ]);

        // Log password change
        SecurityAuditLog::logEvent(
            SecurityAuditLog::EVENT_PASSWORD_CHANGED,
            $user->email,
            $user->id,
            'Password changed by student via profile',
            ['user_role' => 'student'],
            SecurityAuditLog::SEVERITY_INFO
        );

        return back()->with('password_success', 'Password updated successfully.');
    }
}