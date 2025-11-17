<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function __invoke(Request $request): View
    {
        $connection = config('tenancy.database.central_connection');
        $query = User::on($connection);

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
}
