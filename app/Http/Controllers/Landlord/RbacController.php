<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RbacController extends Controller
{
    public function index(): View
    {
        $roles = Role::where('guard_name', 'landlord')->with('permissions')->get();

        // Group permissions by module (e.g., 'users.view' -> 'users')
        $permissions = Permission::where('guard_name', 'landlord')->get()->groupBy(function($perm) {
            $parts = explode('.', $perm->name);
            return count($parts) > 1 ? $parts[0] : 'general';
        });

        return view('landlord.rbac.index', compact('roles', 'permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,NULL,id,guard_name,landlord',
        ]);

        Role::create(['name' => $request->name, 'guard_name' => 'landlord']);

        return redirect()->route('rbac.index')->with('success', __('Role created successfully.'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $role = Role::findById($id, 'landlord');

        if ($request->has('name')) {
             $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,'.$role->id.',id,guard_name,landlord',
            ]);
            $role->update(['name' => $request->name]);
        }

        // Handle permissions update if present (even if empty array, meaning clear all)
        // We check if 'permissions_submitted' is present to distinguish between just renaming and updating permissions
        if ($request->has('permissions_submitted')) {
            $permissions = $request->input('permissions', []);
            // Verify permissions exist and are for landlord guard
            $validPermissions = Permission::whereIn('name', $permissions)
                                        ->where('guard_name', 'landlord')
                                        ->pluck('name')
                                        ->toArray();
            $role->syncPermissions($validPermissions);
        }

        return redirect()->route('rbac.index')->with('success', __('Role updated successfully.'));
    }

    public function destroy($id): RedirectResponse
    {
        $role = Role::findById($id, 'landlord');

        if (in_array($role->name, ['Super Admin', 'Admin'])) {
             return back()->with('error', __('Cannot delete system roles.'));
        }

        $role->delete();
        return redirect()->route('rbac.index')->with('success', __('Role deleted successfully.'));
    }
}
