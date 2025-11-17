<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SecurityAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\UserPreference;

class ProfileController extends Controller
{
    /**
     * Show the teacher profile page.
     */
    public function show()
    {
        $user = Auth::user();
        return view('tenant.teacher.profile.show', compact('user'));
    }

    /**
     * Update basic profile fields for the teacher.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle avatar upload if provided
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile/photos', 'public');
            // Delete old if exists and different
            if ($user->profile_photo && $user->profile_photo !== $path) {
                try {
                    Storage::disk('public')->delete($user->profile_photo);
                } catch (\Throwable $e) {
                }
            }
            $validated['profile_photo'] = $path;
        }

        // Remove profile_photo from validated if not uploaded
        if (!isset($validated['profile_photo'])) {
            unset($validated['profile_photo']);
        }

        // Persist using DB facade to avoid implicit casting surprises
        DB::table('users')->where('id', $user->id)->update(array_merge($validated, ['updated_at' => now()]));

        // Clear the cached user to force reload
        Auth::clearResolvedInstances();
        Auth::guard()->setUser(Auth::user()->fresh());

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Show settings page (preferences and password change).
     */
    public function settings()
    {
        $user = Auth::user();
        // Load preferences from dedicated table (create defaults if none)
        $pref = $user->preference; // may be null
        $prefs = [
            'email_notifications' => $pref ? (bool)$pref->email_notifications : true,
            'sms_notifications' => $pref ? (bool)$pref->sms_notifications : false,
        ];
        return view('tenant.teacher.settings.index', [
            'user' => $user,
            'prefs' => $prefs,
        ]);
    }

    /**
     * Update notification preferences (persisted in users.notes JSON field).
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'email_notifications' => ['sometimes', 'boolean'],
            'sms_notifications' => ['sometimes', 'boolean'],
        ]);

        // Persist into user_preferences table
        $email = (bool)($validated['email_notifications'] ?? false);
        $sms = (bool)($validated['sms_notifications'] ?? false);
        $user->preference()->updateOrCreate([], [
            'email_notifications' => $email,
            'sms_notifications' => $sms,
        ]);

        return back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Update password for teacher.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', new \App\Rules\SecurePassword()],
        ]);

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make($request->input('password')),
        ]);

        // Log password change
        SecurityAuditLog::logEvent(
            SecurityAuditLog::EVENT_PASSWORD_CHANGED,
            $user->email,
            $user->id,
            'Password changed by teacher via profile',
            ['user_role' => 'teacher'],
            SecurityAuditLog::SEVERITY_INFO
        );

        return back()->with('success', 'Password updated successfully.');
    }
}