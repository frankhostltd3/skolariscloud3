<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'view countries',
            'manage countries',
            'view education levels',
            'manage education levels',
            'view grading systems',
            'manage grading systems',
            'view examination bodies',
            'manage examination bodies',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = ['Admin', 'Super Admin', 'admin', 'super-admin'];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($permissions);
            }
        }
    }

    public function down(): void
    {
        // We don't necessarily want to remove permissions in down() as they might be used elsewhere
    }
};
