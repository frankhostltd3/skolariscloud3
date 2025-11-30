<?php

namespace App\Http\Controllers\Landlord\Auth;

use App\Http\Controllers\Controller;
use App\Models\LandlordAuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\GuardDoesNotMatch;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

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
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('landlord')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $this->ensureLandlordAccess();

        // Audit: landlord login
        try {
            $user = Auth::guard('landlord')->user();
            LandlordAuditLog::create([
                'user_id' => $user->id,
                'action' => 'landlord_login',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'context' => [ 'email' => $request->email ],
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
        $user = Auth::guard('landlord')->user();
        if ($user === null) {
            return;
        }

        // Temporarily force the default connection to the central database
        $originalDefaultConnection = config('database.default');
        config(['database.default' => 'mysql']);

        try {
            $permissionName = 'access landlord dashboard';
            $guardName = 'landlord';

            // Find or create the permission on the central connection
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => $guardName]
            );

            // Find or create the role on the central connection
            $roleName = 'Landlord Admin';
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => $guardName]
            );

            // Grant the permission to the role (ignore if already exists)
            try {
                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                // Permission already assigned to role, ignore
            }

            // Assign the role to the user (ignore if already exists)
            try {
                // Manually check if the user has the role to avoid Spatie's tenant-aware checks
                $hasRole = \Illuminate\Support\Facades\DB::connection('mysql')
                    ->table('model_has_roles')
                    ->where('model_id', $user->id)
                    ->where('model_type', get_class($user))
                    ->where('role_id', $role->id)
                    ->exists();

                if (!$hasRole) {
                    // Manually insert the role assignment
                    \Illuminate\Support\Facades\DB::connection('mysql')
                        ->table('model_has_roles')
                        ->insert([
                            'role_id' => $role->id,
                            'model_type' => get_class($user),
                            'model_id' => $user->id,
                            'tenant_id' => 'landlord', // Use 'landlord' string
                        ]);
                }
            } catch (\Exception $e) {
                // Role assignment failed, ignore
            }

            // Manually assign permission directly to user as well (backup)
            try {
                $hasPermission = \Illuminate\Support\Facades\DB::connection('mysql')
                    ->table('model_has_permissions')
                    ->where('model_id', $user->id)
                    ->where('model_type', get_class($user))
                    ->where('permission_id', $permission->id)
                    ->exists();

                if (!$hasPermission) {
                    \Illuminate\Support\Facades\DB::connection('mysql')
                        ->table('model_has_permissions')
                        ->insert([
                            'permission_id' => $permission->id,
                            'model_type' => get_class($user),
                            'model_id' => $user->id,
                            'tenant_id' => 'landlord', // Use 'landlord' string
                        ]);
                }
            } catch (\Exception $e) {
                // Permission assignment failed, ignore
            }

            // Clear the cache to apply changes immediately
            app(PermissionRegistrar::class)->forgetCachedPermissions();

        } finally {
            // IMPORTANT: Restore the original default connection
            config(['database.default' => $originalDefaultConnection]);
        }
    }
}
