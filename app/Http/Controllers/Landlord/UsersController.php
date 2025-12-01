<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\LandlordUser;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\PermissionRegistrar;

class UsersController extends Controller
{
    public function __invoke(Request $request): View
    {
        $connection = central_connection();
        $query = LandlordUser::on($connection)
            ->whereNull('school_id'); // landlord records live in central DB only

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role = trim((string) $request->get('role'))) {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role)->where('guard_name', 'landlord');
            });
        }

        $users = $query->with(['roles'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Fetch available landlord roles for filter dropdown
        $roles = Role::on($connection)
            ->where('guard_name', 'landlord')
            ->orderBy('name')
            ->pluck('name')
            ->all();

        return view('landlord.users.index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => [
                'search' => $search ?? '',
                'role' => $role ?? '',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $connection = central_connection();

        $validator = validator($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:50'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('showInviteModal', true);
        }

        $data = $validator->validated();

        $user = LandlordUser::on($connection)->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Str::random(32),
            'user_type' => \App\Enums\UserType::ADMIN,
            'school_id' => null,
            'approval_status' => 'approved',
            'is_active' => true,
        ]);

        $roles = $data['roles'] ?? [];
        if (! empty($roles)) {
            $validRoles = Role::on($connection)
                ->where('guard_name', 'landlord')
                ->whereIn('name', $roles)
                ->pluck('name')
                ->all();

            if ($validRoles) {
                $registrar = app(PermissionRegistrar::class);
                $registrar->setPermissionsTeamId('landlord');
                $user->syncRoles($validRoles);
                $registrar->forgetCachedPermissions();
            }
        }

        // Send password reset / invite email
        Password::broker('landlords')->sendResetLink(['email' => $user->email]);

        return back()->with('success', __('Invitation sent to :email. They will receive a password setup link.', ['email' => $user->email]));
    }

    public function updateRoles(Request $request, LandlordUser $user): RedirectResponse
    {
        $data = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string'],
        ]);

        $connection = central_connection();
        $requestedRoles = $data['roles'] ?? [];

        $validRoles = empty($requestedRoles)
            ? []
            : Role::on($connection)
                ->where('guard_name', 'landlord')
                ->whereIn('name', $requestedRoles)
                ->pluck('name')
                ->all();

        $registrar = app(PermissionRegistrar::class);
        $registrar->setPermissionsTeamId('landlord');

        $user->syncRoles($validRoles);

        $registrar->forgetCachedPermissions();

        return back()->with('success', __('Roles updated for :name.', ['name' => $user->name]));
    }

    public function destroy(Request $request, LandlordUser $user): RedirectResponse
    {
        $current = $request->user('landlord');
        if ($current && $current->is($user)) {
            return back()->with('error', __('You cannot remove the account you are currently using.'));
        }

        $user->delete();

        return back()->with('success', __('User :name has been removed.', ['name' => $user->name]));
    }
}
