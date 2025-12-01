<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\LandlordUser;
use Illuminate\Support\Facades\Hash;

class LandlordPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create landlord guard permissions
        $permissions = [
            'access landlord dashboard',
            'manage tenants',
            'view billing',
            'manage landlord billing',
            'manage invoices',
            'manage payment methods',
            'view analytics',
            'manage settings',
            'view audit logs',
            'manage notifications',
            'manage users',
            'view system health',
            'manage integrations',
            'create tenants',
            'edit tenants',
            'delete tenants',
            'export tenants',
            'import tenants',
        ];

        $this->command->info('Creating landlord permissions...');

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'landlord',
                'tenant_id' => 'landlord',
            ]);
            $this->command->info("✓ Created permission: {$permission}");
        }

        // Create landlord-admin role
        $this->command->info('Creating landlord-admin role...');
        $role = Role::firstOrCreate([
            'name' => 'landlord-admin',
            'guard_name' => 'landlord',
            'tenant_id' => 'landlord',
        ]);

        $role->syncPermissions($permissions);
        $this->command->info("✓ Created landlord-admin role with all permissions");

        // Check if landlord user exists, if not create one
        $landlordEmail = 'admin@landlord.local';
        $landlordUser = LandlordUser::where('email', $landlordEmail)->first();

        if (!$landlordUser) {
            $this->command->info('Creating landlord user...');
            $landlordUser = LandlordUser::create([
                'name' => 'Landlord Admin',
                'email' => $landlordEmail,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);
            $this->command->info("✓ Created landlord user: {$landlordEmail}");
        }

        // Assign role to user
        // Set the team id to 'landlord' for this assignment
        setPermissionsTeamId('landlord');

        if (!$landlordUser->hasRole('landlord-admin', 'landlord')) {
            $landlordUser->assignRole($role);
            $this->command->info("✓ Assigned landlord-admin role to user");
        }

        $this->command->newLine();
        $this->command->info('Landlord permissions and user setup complete!');
        $this->command->info("Login at: /landlord/login");
        $this->command->info("Email: {$landlordEmail}");
        $this->command->info("Password: password123");
    }
}
