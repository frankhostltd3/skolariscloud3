<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login form.
     */
    public function create(): View
    {
        return view('tenant.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Rate limiting
        $this->ensureIsNotRateLimited($request);

        // Attempt authentication
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request), 300); // 5 minutes

            throw ValidationException::withMessages([
                'email' => __('The provided credentials are incorrect.'),
            ]);
        }

        $user = Auth::user();

        // Check if 2FA is enabled system-wide AND user has 2FA enabled
        if (setting('two_factor_enabled', false) && $user->two_factor_enabled) {
            // Logout temporarily
            Auth::logout();
            
            // Store user ID and remember preference for 2FA challenge
            $request->session()->put([
                'two_factor_user_id' => $user->id,
                'two_factor_remember' => $request->boolean('remember'),
            ]);
            
            // Redirect to 2FA challenge
            return redirect()->route('tenant.2fa.challenge')
                ->with('info', 'Please enter your two-factor authentication code.');
        }
        
        // Complete login
        $request->session()->regenerate();
        RateLimiter::clear($this->throttleKey($request));
        
    // Update last activity
    $user->update(['last_activity_at' => now()]);

    // Normalize roles (auto-attach canonical capitalized roles if only lowercase or legacy variants exist)
    $this->normalizeUserRoles($user);

    // Redirect based on user role
    return $this->redirectBasedOnRole($user);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        
        // Clear 2FA session data
        $request->session()->forget([
            'two_factor_verified',
            'two_factor_verified_at',
            'two_factor_user_id',
            'two_factor_remember'
        ]);
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $locale = LaravelLocalization::getCurrentLocale() ?? app()->getLocale() ?? config('app.locale', 'en');
        $locale = trim((string) $locale) !== '' ? $locale : 'en';

        if (Route::has('landing')) {
            return redirect()->route('landing', ['locale' => $locale])
                ->with('success', 'You have been logged out successfully.');
        }

        $guestHomeUrl = LaravelLocalization::localizeURL('/', $locale);

        return redirect()->to($guestHomeUrl)
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Redirect user based on their role.
     */
    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        // Check for Admin role (case-insensitive)
        if ($user->hasAnyRole(['Admin', 'admin'])) {
            return redirect()->route('tenant.admin')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }
        
        // Check for Staff/Teacher role (case-insensitive)
        if ($user->hasAnyRole(['Staff', 'staff', 'Teacher', 'teacher'])) {
            return redirect()->route('tenant.teacher.dashboard')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }
        
        // Check for Student role (case-insensitive)
        if ($user->hasAnyRole(['Student', 'student'])) {
            return redirect()->route('tenant.student')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }
        
        // Check for Parent role (case-insensitive)
        if ($user->hasAnyRole(['Parent', 'parent'])) {
            return redirect()->route('tenant.parent')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Default fallback
        return redirect()->route('tenant.dashboard')
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Ensure the login request is not rate limited.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }

    /**
     * Ensure user has canonical capitalized roles. If a lowercase variant exists (e.g., 'student'),
     * attach the canonical 'Student'. If legacy 'teacher' exists, map to 'Staff' (primary role used for portal access)
     * while keeping Teacher compatibility if needed for future permission groups.
     */
    protected function normalizeUserRoles(User $user): void
    {
        if (! method_exists($user, 'getRoleNames')) {
            return; // Not using spatie/permission on this model
        }

        $current = $user->getRoleNames()->toArray();
        if (empty($current)) {
            return; // No roles yet; leave logic to other flows (e.g., post-registration)
        }

        $needSave = false;
        $map = [
            'admin' => 'Admin',
            'staff' => 'Staff',
            'teacher' => 'Staff', // unify teacher into Staff for dashboard gating
            'student' => 'Student',
            'parent' => 'Parent',
        ];

        foreach ($current as $roleName) {
            $lc = strtolower($roleName);
            if (isset($map[$lc]) && ! $user->hasRole($map[$lc])) {
                try {
                    // Ensure canonical role exists (firstOrCreate) using same guard/team context
                    $teamKey = config('permission.column_names.team_foreign_key', 'team_id');
                    $teamId = function_exists('tenant') && tenant() ? tenant()->getTenantKey() : null;
                    $attrs = [
                        'name' => $map[$lc],
                        'guard_name' => 'web',
                    ];
                    if ($teamId !== null) {
                        $attrs[$teamKey] = $teamId;
                    }
                    \Spatie\Permission\Models\Role::firstOrCreate($attrs);
                    $user->assignRole($map[$lc]);
                    $needSave = true;
                } catch (\Throwable $e) {
                    // Silently ignore; normalization is best-effort
                }
            }
        }

        // Optionally we could detach lowercase duplicates; keeping them avoids breaking existing checks.
        if ($needSave) {
            // Clear permission cache so new role is recognized immediately.
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }
}
