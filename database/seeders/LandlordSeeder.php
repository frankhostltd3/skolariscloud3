<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LandlordSeeder extends Seeder
{
    public function run(): void
    {
        // Create the permission
        $permission = Permission::firstOrCreate(
            ['name' => 'access landlord dashboard', 'guard_name' => 'landlord']
        );

        // Create the Super Admin Role
        $role = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'landlord']
        );

        $role->givePermissionTo($permission);

        // Create the Landlord Admin User
        $user = User::firstOrCreate(
            ['email' => 'admin@skolaris.cloud'],
            [
                'name' => 'Landlord Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'user_type' => UserType::ADMIN,
                'school_id' => null,
                'approval_status' => 'approved',
            ]
        );

        // Assign the role to the user
        // We need to specify the guard because the user model might default to 'web' or 'tenant'
        // But assignRole should handle it if the role has the correct guard.
        // However, model_has_roles table needs to be populated.
        
        // Since the user is in the central DB, and roles are in the central DB (presumably),
        // this should work.
        $user->assignRole($role);
        
        $this->command->info('Landlord Admin created successfully.');
        $this->command->info('Email: admin@skolaris.cloud');
        $this->command->info('Password: password');
    }
}
