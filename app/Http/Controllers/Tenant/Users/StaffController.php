<?php

namespace App\Http\Controllers\Tenant\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    private $roles = ['Staff', 'staff'];
    private $title = 'Staff';
    private $routePrefix = 'tenant.users.staff';
    private ?array $resolvedRoles = null;

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->role($this->getResolvedRoles())
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('tenant.users.shared.index', [
            'title' => __($this->title),
            'routePrefix' => $this->routePrefix,
            'users' => $users,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('tenant.users.shared.create', [
            'title' => __($this->title),
            'routePrefix' => $this->routePrefix,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create($data);
        $user->assignRole($this->resolveRoleName());

        return redirect()->route($this->routePrefix)->with('success', __(':title created.', ['title' => __($this->title)]));
    }

    public function show(User $user): View
    {
        abort_unless($user->hasAnyRole($this->getResolvedRoles()), 404);

        return view('tenant.users.shared.show', [
            'title' => __($this->title),
            'routePrefix' => $this->routePrefix,
            'user' => $user,
        ]);
    }

    public function edit(User $user): View
    {
        abort_unless($user->hasAnyRole($this->getResolvedRoles()), 404);

        return view('tenant.users.shared.edit', [
            'title' => __($this->title),
            'routePrefix' => $this->routePrefix,
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->hasAnyRole($this->getResolvedRoles()), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        if (! $user->hasAnyRole($this->roles)) {
            $user->syncRoles([$this->resolveRoleName()]);
        }

        return redirect()->route($this->routePrefix . '.show', $user)->with('success', __(':title updated.', ['title' => __($this->title)]));
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless($user->hasAnyRole($this->getResolvedRoles()), 404);

        $user->delete();

        return redirect()->route($this->routePrefix)->with('success', __(':title deleted.', ['title' => __($this->title)]));
    }

    public function activate(User $user): RedirectResponse
    {
        abort_unless($user->hasAnyRole($this->getResolvedRoles()), 404);

        $user->activate();

        return redirect()->back()->with('success', __('User activated successfully.'));
    }

    public function deactivate(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->hasAnyRole($this->getResolvedRoles()), 404);

        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $user->deactivate($request->input('reason'));

        return redirect()->back()->with('success', __('User deactivated successfully.'));
    }

    private function resolveRoleName(): string
    {
        return $this->getResolvedRoles()[0] ?? Role::findOrCreate($this->roles[0], 'web')->name;
    }

    private function getResolvedRoles(): array
    {
        if ($this->resolvedRoles !== null) {
            return $this->resolvedRoles;
        }

        $names = Role::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $this->roles)
            ->pluck('name')
            ->all();

        if (empty($names)) {
            $names[] = Role::findOrCreate($this->roles[0], 'web')->name;
        }

        return $this->resolvedRoles = $names;

        $role = Role::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $this->roles)
            ->first();

        if ($role) {
            return $this->resolvedRole = $role->name;
        }

        $role = Role::findOrCreate($this->roles[0], 'web');

        return $this->resolvedRole = $role->name;
    }
}
