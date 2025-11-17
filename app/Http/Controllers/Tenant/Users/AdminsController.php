<?php

namespace App\Http\Controllers\Tenant\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminsController extends Controller
{
    private string $role = 'Admin';
    private string $title = 'Administrators';
    private string $routePrefix = 'tenant.users.admins';

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $users = User::query()
            ->role($this->role)
            ->when($q !== '', fn($query) => $query->where(function($w) use ($q){
                $w->where('name','like',"%$q%");
                $w->orWhere('email','like',"%$q%");
            }))
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
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required', 'confirmed', new \App\Rules\SecurePassword()],
        ]);

        $user = User::create($data);
        $user->assignRole($this->role);

        return redirect()->route($this->routePrefix)->with('success', __(':title created.', ['title' => __($this->title)]));
    }

    public function show(User $user): View
    {
        abort_unless($user->hasRole($this->role), 404);
        return view('tenant.users.shared.show', [
            'title' => __($this->title),
            'routePrefix' => $this->routePrefix,
            'user' => $user,
        ]);
    }

    public function edit(User $user): View
    {
        abort_unless($user->hasRole($this->role), 404);
        return view('tenant.users.shared.edit', [
            'title' => __($this->title),
            'routePrefix' => $this->routePrefix,
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->hasRole($this->role), 404);
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email,' . $user->id],
            'password' => ['nullable','confirmed','min:8'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        if (! $user->hasRole($this->role)) {
            $user->syncRoles([$this->role]);
        }

        return redirect()->route($this->routePrefix . '.show', $user)->with('success', __(':title updated.', ['title' => __($this->title)]));
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless($user->hasRole($this->role), 404);
        $user->delete();
        return redirect()->route($this->routePrefix)->with('success', __(':title deleted.', ['title' => __($this->title)]));
    }

    public function activate(User $user): RedirectResponse
    {
        abort_unless($user->hasRole($this->role), 404);
        
        $user->activate();
        
        return redirect()->back()->with('success', __('User activated successfully.'));
    }

    public function deactivate(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->hasRole($this->role), 404);
        
        // Prevent admin from deactivating themselves
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', __('You cannot deactivate your own account.'));
        }
        
        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);
        
        $user->deactivate($request->input('reason'));
        
        return redirect()->back()->with('success', __('User deactivated successfully.'));
    }
}
