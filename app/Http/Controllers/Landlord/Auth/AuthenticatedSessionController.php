<?php

namespace App\Http\Controllers\Landlord\Auth;

use App\Http\Controllers\Controller;
use App\Models\LandlordAuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\GuardDoesNotMatch;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Stancl\Tenancy\Database\Models\Tenant;

class AuthenticatedSessionController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::guard('landlord')->check()) {
            return redirect()->route('landlord.dashboard');
        }

        return view('landlord.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::guard('landlord')->attempt($credentials, $remember)) {
            return back()
                ->withErrors([
                    'email' => __('These credentials do not match our records.'),
                ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $this->ensureLandlordAccess();

        // Audit: landlord login
        try {
            LandlordAuditLog::create([
                'user_id' => optional(Auth::guard('landlord')->user())->id,
                'action' => 'landlord_login',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'context' => [ 'email' => $credentials['email'] ],
            ]);
        } catch (\Throwable $e) {
            // swallow audit failures
        }

        return redirect()->intended(route('landlord.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        // Capture user before logout for audit
        $userId = optional(Auth::guard('landlord')->user())->id;
        $ip = $request->ip();
        $ua = $request->userAgent();

        Auth::guard('landlord')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Audit: landlord logout
        try {
            LandlordAuditLog::create([
                'user_id' => $userId,
                'action' => 'landlord_logout',
                'ip_address' => $ip,
                'user_agent' => $ua,
                'context' => null,
            ]);
        } catch (\Throwable $e) {}

        return redirect()->route('landlord.login.show');
    }

    protected function ensureLandlordAccess(): void
    {
        $guard = Auth::guard('landlord');
        $user = $guard->user();

        if ($user === null) {
            return;
        }

        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);
        $teamId = config('app.landlord_team_id', 'skolaris-root');

        Tenant::withoutEvents(fn () => Tenant::query()->firstOrCreate([
            'id' => $teamId,
        ], [
            'data' => [],
        ]));
        $registrar->setPermissionsTeamId($teamId);

        // Create permission first, but reuse existing record on sqlite where the unique index ignores tenant_id
        $permission = Permission::query()
            ->where('name', 'access landlord dashboard')
            ->where('guard_name', 'landlord')
            ->first();

        if (! $permission) {
            $permission = Permission::query()->create([
                'tenant_id' => $teamId,
                'name' => 'access landlord dashboard',
                'guard_name' => 'landlord',
            ]);
        } elseif (! $permission->tenant_id) {
            $permission->forceFill(['tenant_id' => $teamId])->save();
        }

        // Flush cache to ensure permission is available
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Now safely give permission to user
        try {
            if (! $user->hasPermissionTo($permission->name, 'landlord')) {
                $user->givePermissionTo($permission);
            }
        } catch (\Exception $e) {
            // If permission check fails, just assign it directly
            $user->givePermissionTo($permission);
        }

        // Handle role assignment
        $role = Role::query()->where([
            'tenant_id' => $teamId,
            'name' => 'Landlord Admin',
            'guard_name' => 'landlord',
        ])->first();

        if ($role !== null) {
            try {
                if (! $user->hasRole($role->name, 'landlord')) {
                    $user->assignRole($role);
                }
            } catch (GuardDoesNotMatch | \Exception $e) {
                // Fallback: ensure user has at least the permission
                $user->givePermissionTo($permission);
            }
        }
    }
}
