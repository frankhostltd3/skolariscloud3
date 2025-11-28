<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AdminRegistrationController extends Controller
{
    /**
     * Display the admin registration form.
     */
    public function create(): View
    {
        // Check if admin registration is allowed
        $adminExists = User::role('Admin')->exists();
        
        // Allow registration if no admin exists OR if admin_registration_token is valid
        $token = request()->query('token');
        $validToken = setting('admin_registration_token');
        
        if ($adminExists && (!$token || $token !== $validToken)) {
            abort(403, 'Admin registration is not available.');
        }

        return view('tenant.auth.admin-register');
    }

    /**
     * Handle an incoming admin registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if admin registration is allowed
        $adminExists = User::role('Admin')->exists();
        $token = $request->input('token');
        $validToken = setting('admin_registration_token');
        
        if ($adminExists && (!$token || $token !== $validToken)) {
            abort(403, 'Admin registration is not available.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $schoolId = config('tenant.school_id');
        if (!$schoolId && app()->bound('currentSchool')) {
            $schoolId = app('currentSchool')->id;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), // Auto-verify admin
            'password_changed_at' => now(),
            'school_id' => $schoolId,
        ]);

        // Assign Admin role
        $user->assignRole('Admin');

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('tenant.admin')
            ->with('success', 'Admin account created successfully!');
    }
}
