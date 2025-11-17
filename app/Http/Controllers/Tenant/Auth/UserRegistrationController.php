<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserRegistrationController extends Controller
{
    /**
     * Display the registration form.
     */
    public function create(): View
    {
        // Check if public registration is enabled
        if (!setting('public_registration_enabled', true)) {
            abort(403, 'Registration is currently disabled.');
        }

        return view('tenant.auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if public registration is enabled
        if (!setting('public_registration_enabled', true)) {
            abort(403, 'Registration is currently disabled.');
        }

        $roleOptions = ['Staff', 'Student', 'Parent'];

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in($roleOptions)],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
            'email_verified_at' => setting('email_verification_required') ? null : now(),
            'approval_status' => 'pending', // New users require approval
        ]);
        
        // Ensure role exists for this tenant (guards against mis-seeded tenants)
        $roleMap = [
            'Staff' => 'staff',
            'Student' => 'student',
            'Parent' => 'parent',
        ];
        $requestedRole = $request->role;
        $roleName = $roleMap[$requestedRole] ?? strtolower($requestedRole);
        
        // In a tenancy context, we may be using team_id scoping; replicate seeding logic
        $teamKey = config('permission.column_names.team_foreign_key', 'team_id');
        $teamId = function_exists('tenant') && tenant() ? tenant()->getTenantKey() : null;
        
        try {
            if ($teamId !== null) {
                // Set team context so spatie/permission attaches correctly
                app(PermissionRegistrar::class)->setPermissionsTeamId($teamId);
            }
            
            // Ensure role exists for this tenant
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
                $teamKey => $teamId,
            ]);
            
            // Assign role to user
            $user->assignRole($roleName);
            
            // Reset permission team context after successful assignment
            if ($teamId !== null) {
                app(PermissionRegistrar::class)->setPermissionsTeamId(null);
            }
        } catch (\Throwable $e) {
            // Reset team context on error
            if ($teamId !== null) {
                app(PermissionRegistrar::class)->setPermissionsTeamId(null);
            }
            
            // Clean up created user if role assignment fails
            $user->delete();
            return back()->withInput()->withErrors([
                'role' => __('Unable to assign role (:role). Please contact support or try again later.', ['role' => $requestedRole]),
            ]);
        }

        event(new Registered($user));

        // Send pending approval notification to user
        // Temporarily disabled until mail server is configured
        // $user->notify(new \App\Notifications\UserRegistrationPendingNotification());

        // TODO: Optionally notify admins about new pending registration
        // User::role('Admin')->each(fn($admin) => $admin->notify(new NewUserRegistrationNotification($user)));

        // Log the user in (they'll see pending approval page)
        Auth::login($user);

        // Redirect to pending approval page
        return redirect()->route('pending-approval')
            ->with('success', __('Registration successful! Your account is pending approval.'));
    }

    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        if ($user->hasRole(['Staff', 'staff'])) {
            return redirect()->route('tenant.teacher.dashboard')
                ->with('success', 'Welcome! Your account has been created.');
        }

        if ($user->hasRole(['Student', 'student'])) {
            return redirect()->route('tenant.student')
                ->with('success', 'Welcome! Your account has been created.');
        }

        if ($user->hasRole(['Parent', 'parent'])) {
            return redirect()->route('tenant.parent')
                ->with('success', 'Welcome! Your account has been created.');
        }

        // Default
        return redirect()->route('tenant.dashboard')
            ->with('success', 'Welcome! Your account has been created.');
    }
}
