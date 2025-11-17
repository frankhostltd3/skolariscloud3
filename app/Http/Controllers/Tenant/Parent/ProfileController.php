<?php

namespace App\Http\Controllers\Tenant\Parent;

use App\Http\Controllers\Controller;
use App\Models\SecurityAuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index(): View
    {
    $user = Auth::user()->loadMissing(['parentProfile.students.class', 'parentProfile.students.stream']);
    $parent = $user->parentProfile;
    $children = $parent?->students ?? collect();

    return view('tenant.parent.profile', compact('user', 'parent', 'children'));
    }

    public function update(Request $request): RedirectResponse
    {
    $user = Auth::user()->loadMissing('parentProfile');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'parent_number' => 'nullable|string|max:50',
            'relationship' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
        ]);

        // Update basic profile info
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
            'gender' => $validated['gender'] ?? $user->gender,
            'address' => $validated['address'] ?? $user->address,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? $user->emergency_contact_name,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? $user->emergency_contact_phone,
        ]);

        // Update parent info if exists
        if ($user->parentProfile) {
            $user->parentProfile->update([
                'parent_number' => $validated['parent_number'] ?? $user->parentProfile->parent_number,
                'relationship' => $validated['relationship'] ?? $user->parentProfile->relationship,
                'occupation' => $validated['occupation'] ?? $user->parentProfile->occupation,
            ]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function settings(): View
    {
    $user = Auth::user();

        return view('tenant.parent.settings', compact('user'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'grade_notifications' => 'nullable|boolean',
            'attendance_notifications' => 'nullable|boolean',
            'fee_notifications' => 'nullable|boolean',
            'event_notifications' => 'nullable|boolean',
            'share_contact_with_teachers' => 'nullable|boolean',
            'share_progress_with_child' => 'nullable|boolean',
        ]);

        // Prepare notification preferences
        $preferences = [
            'email_notifications' => $validated['email_notifications'] ?? false,
            'sms_notifications' => $validated['sms_notifications'] ?? false,
            'grade_notifications' => $validated['grade_notifications'] ?? false,
            'attendance_notifications' => $validated['attendance_notifications'] ?? false,
            'fee_notifications' => $validated['fee_notifications'] ?? false,
            'event_notifications' => $validated['event_notifications'] ?? false,
            'share_contact_with_teachers' => $validated['share_contact_with_teachers'] ?? false,
            'share_progress_with_child' => $validated['share_progress_with_child'] ?? false,
        ];

        // Update user notification preferences
        $user->update([
            'notification_preferences' => json_encode($preferences),
        ]);

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function changePassword(): View
    {
        $user = Auth::user();

        return view('tenant.parent.change-password', compact('user'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', new \App\Rules\SecurePassword()],
        ]);

        // Check current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password
        $user->update(['password' => Hash::make($validated['password'])]);

        // Log password change
        SecurityAuditLog::logEvent(
            SecurityAuditLog::EVENT_PASSWORD_CHANGED,
            $user->email,
            $user->id,
            'Password changed by parent via profile',
            ['user_role' => 'parent'],
            SecurityAuditLog::SEVERITY_INFO
        );

        return redirect()->back()->with('success', 'Password changed successfully.');
    }
}