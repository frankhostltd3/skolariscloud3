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
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $student = $user->student; // Assuming user has student relationship

        return view('tenant.student.profile', compact('user', 'student'));
    }

    /**
     * Update the student's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $student = $user->student;

        $validated = $request->validate([
            // User fields
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
            // Student-specific fields
            'nationality' => ['nullable', 'string', 'max:100'],
            'religion' => ['nullable', 'string', 'max:100'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_relationship' => ['nullable', 'string', 'max:50'],
            'guardian_phone' => ['nullable', 'string', 'max:20'],
            'guardian_email' => ['nullable', 'email', 'max:255'],
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            // Store new photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo'] = $path;
        }

        // Update user record
        DB::table('users')->where('id', $user->id)->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'profile_photo' => $validated['profile_photo'] ?? $user->profile_photo,
            'updated_at' => now(),
        ]);

        // Update student record if exists
        if ($student) {
            DB::table('students')->where('id', $student->id)->update([
                'nationality' => $validated['nationality'] ?? $student->nationality,
                'religion' => $validated['religion'] ?? $student->religion,
                'guardian_name' => $validated['guardian_name'] ?? $student->guardian_name,
                'guardian_relationship' => $validated['guardian_relationship'] ?? $student->guardian_relationship,
                'guardian_phone' => $validated['guardian_phone'] ?? $student->guardian_phone,
                'guardian_email' => $validated['guardian_email'] ?? $student->guardian_email,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('tenant.student.profile')
            ->with('success', 'Profile updated successfully.');
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
