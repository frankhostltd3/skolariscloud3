<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityAuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        return view('tenant.admin.profile', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old profile photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            // Store new profile photo
            $file = $request->file('profile_photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('profile_photos', $fileName, 'public');
            $validated['profile_photo'] = $filePath;
        }

        // Update basic profile info
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
            'profile_photo' => $validated['profile_photo'] ?? $user->profile_photo,
        ]);

        // Update password if provided
        if (!empty($validated['new_password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $user->update(['password' => Hash::make($validated['new_password'])]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
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
            'Password changed by admin via profile',
            ['user_role' => 'admin'],
            SecurityAuditLog::SEVERITY_INFO
        );

        return back()->with('password_success', 'Password updated successfully.');
    }
}