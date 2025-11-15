<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class PermissionsController extends Controller
{
    /**
     * Display the permissions and access control page
     */
    public function index()
    {
        $roles = Role::withCount('users')->with('permissions')->get();

        // Group permissions by module
        $allPermissions = Permission::all();
        $permissionGroups = $allPermissions->groupBy(function ($permission) {
            // Extract module name from permission (e.g., 'users.create' => 'Users')
            $parts = explode('.', $permission->name);
            return ucfirst($parts[0]);
        });

        // Get current settings from database
        $settings = $this->getSettings();

        return view('tenant.settings.admin.permissions', compact('roles', 'permissionGroups', 'settings'));
    }

    /**
     * Update access control settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // Default role assignments
            'default_student_role' => 'nullable|string',
            'default_teacher_role' => 'nullable|string',

            // Login & Authentication
            'allow_student_login' => 'nullable|boolean',
            'allow_parent_login' => 'nullable|boolean',
            'allow_teacher_login' => 'nullable|boolean',
            'require_email_verification' => 'nullable|boolean',
            'allow_password_reset' => 'nullable|boolean',
            'enable_two_factor' => 'nullable|boolean',
            'allow_registration' => 'nullable|boolean',
            'require_strong_password' => 'nullable|boolean',

            // Password & Security Policy
            'min_password_length' => 'nullable|integer|min:6|max:32',
            'password_expiry_days' => 'nullable|integer|min:0|max:365',
            'max_login_attempts' => 'nullable|integer|min:1|max:20',

            // Session Management
            'session_timeout' => 'nullable|integer|min:5|max:480',
            'remember_me_days' => 'nullable|integer|min:1|max:365',

            // IP Restrictions
            'restrict_by_ip' => 'nullable|boolean',
            'allowed_ips' => 'nullable|string',

            // Role-Based Feature Access
            'teacher_manage_students' => 'nullable|boolean',
            'teacher_manage_classes' => 'nullable|boolean',
            'student_view_reports' => 'nullable|boolean',
        ]);

        // Convert checkboxes to boolean (they won't be in request if unchecked)
        $booleanFields = [
            'allow_student_login', 'allow_parent_login', 'allow_teacher_login',
            'require_email_verification', 'allow_password_reset', 'enable_two_factor',
            'allow_registration', 'require_strong_password', 'restrict_by_ip',
            'teacher_manage_students', 'teacher_manage_classes', 'student_view_reports'
        ];

        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field) ? 1 : 0;
        }

        // Save each setting to database
        foreach ($validated as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }

        // Clear cache
        cache()->forget('settings');

        return redirect()->route('tenant.settings.admin.permissions')
            ->with('success', 'Access control settings updated successfully');
    }

    /**
     * Store a new role
     */
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        Role::create([
            'name' => strtolower(str_replace(' ', '-', $validated['name'])),
            'guard_name' => 'web',
        ]);

        return redirect()->route('tenant.settings.admin.permissions')
            ->with('success', "Role '{$validated['display_name']}' created successfully");
    }

    /**
     * Get role permissions (AJAX)
     */
    public function getRolePermissions($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);

        return response()->json([
            'permissions' => $role->permissions->pluck('name')->toArray()
        ]);
    }

    /**
     * Update role permissions
     */
    public function updateRolePermissions(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $permissions = $validated['permissions'] ?? [];
        $role->syncPermissions($permissions);

        return redirect()->route('tenant.settings.admin.permissions')
            ->with('success', "Permissions updated for role '{$role->name}'");
    }

    /**
     * Delete a role
     */
    public function destroyRole($roleId)
    {
        $role = Role::findOrFail($roleId);

        // Prevent deletion of system roles
        if (in_array($role->name, ['super-admin', 'admin', 'teacher', 'student', 'parent'])) {
            return redirect()->route('tenant.settings.admin.permissions')
                ->with('error', 'Cannot delete system role');
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()->route('tenant.settings.admin.permissions')
            ->with('success', "Role '{$roleName}' deleted successfully");
    }

    /**
     * Sync user registry with roles
     */
    public function syncRegistry(Request $request)
    {
        $validated = $request->validate([
            'sync_role' => 'required|exists:roles,id',
            'notify_sync_complete' => 'nullable|boolean',
        ]);

        $role = Role::findOrFail($validated['sync_role']);

        // This is a placeholder for actual sync logic
        // You would implement your specific registry sync requirements here

        return redirect()->route('tenant.settings.admin.permissions')
            ->with('success', "Registry sync initiated for role '{$role->name}'");
    }

    /**
     * Bulk assign roles to users
     */
    public function bulkAssignRole(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'user_emails' => 'required|string',
            'notify_bulk_assign_complete' => 'nullable|boolean',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $emails = array_filter(array_map('trim', explode("\n", $validated['user_emails'])));

        $successCount = 0;
        $failedEmails = [];

        foreach ($emails as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->assignRole($role);
                $successCount++;
            } else {
                $failedEmails[] = $email;
            }
        }

        $message = "Assigned role '{$role->name}' to {$successCount} user(s)";
        if (count($failedEmails) > 0) {
            $message .= '. Failed emails: ' . implode(', ', $failedEmails);
        }

        return redirect()->route('tenant.settings.admin.permissions')
            ->with('success', $message);
    }

    /**
     * Clear permissions cache
     */
    public function clearCache()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        cache()->forget('settings');

        return redirect()->route('tenant.settings.admin.permissions')
            ->with('success', 'Permissions cache cleared successfully');
    }

    /**
     * Get all settings from database
     */
    private function getSettings()
    {
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();

        // Set defaults if not in database
        return array_merge([
            'default_student_role' => 'student',
            'default_teacher_role' => 'teacher',
            'allow_student_login' => true,
            'allow_parent_login' => true,
            'allow_teacher_login' => true,
            'require_email_verification' => false,
            'allow_password_reset' => true,
            'enable_two_factor' => false,
            'allow_registration' => false,
            'require_strong_password' => true,
            'min_password_length' => 10,
            'password_expiry_days' => 90,
            'max_login_attempts' => 5,
            'session_timeout' => 60,
            'remember_me_days' => 30,
            'restrict_by_ip' => false,
            'allowed_ips' => '',
            'teacher_manage_students' => false,
            'teacher_manage_classes' => false,
            'student_view_reports' => false,
        ], $settings);
    }
}
