<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        return view('tenant.student.settings', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'language' => 'nullable|string|in:en,sw',
            'theme' => 'nullable|string|in:light,dark',
            'notifications_email' => 'boolean',
            'notifications_sms' => 'boolean',
            'notifications_system' => 'boolean',
            'grades_visible_to_parents' => 'boolean',
            'attendance_reminders' => 'boolean',
            'assignment_notifications' => 'boolean',
            'exam_reminders' => 'boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update user preferences/settings
        $user->update($validated);

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}