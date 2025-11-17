<?php

namespace App\Http\Controllers\Tenant\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $employee = $user->employee; // Assuming user has employee relationship

        return view('tenant.staff.profile', compact('user', 'employee'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:500',
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'employee_number' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && \Storage::disk('public')->exists($user->profile_photo)) {
                \Storage::disk('public')->delete($user->profile_photo);
            }

            // Store new photo
            $photoPath = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo'] = $photoPath;
        }

        // Update basic profile info
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
            'gender' => $validated['gender'] ?? $user->gender,
            'date_of_birth' => $validated['date_of_birth'] ?? $user->date_of_birth,
            'address' => $validated['address'] ?? $user->address,
            'qualification' => $validated['qualification'] ?? $user->qualification,
            'specialization' => $validated['specialization'] ?? $user->specialization,
            'profile_photo' => $validated['profile_photo'] ?? $user->profile_photo,
        ]);

        // Update employee info if exists
        if ($user->employee) {
            $user->employee->update([
                'employee_number' => $validated['employee_number'] ?? $user->employee->employee_number,
                'department' => $validated['department'] ?? $user->employee->department,
                'position' => $validated['position'] ?? $user->employee->position,
            ]);
        }

        // Update password if provided
        if (!empty($validated['new_password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $user->update(['password' => Hash::make($validated['new_password'])]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}