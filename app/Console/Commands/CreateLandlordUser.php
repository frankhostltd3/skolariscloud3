<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CreateLandlordUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'landlord:create-user 
                            {--name= : The name of the landlord user}
                            {--email= : The email address}
                            {--password= : The password (if not provided, will be prompted)}
                            {--role=landlord-admin : The role to assign}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new landlord user with proper permissions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🏢 Creating Landlord User');
        $this->newLine();

        // Get user details
        $name = $this->option('name') ?: $this->ask('Enter the name');
        $email = $this->option('email') ?: $this->ask('Enter the email address');
        $password = $this->option('password') ?: $this->secret('Enter the password');
        $role = $this->option('role');

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        try {
            PermissionRegistrar::setPermissionsTeamId('skolaris-root');

            // Create user on central connection
            $user = User::on(config('tenancy.database.central_connection'))->create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);

            // Ensure landlord guard role exists
            // Use 'skolaris-root' tenant as the team_id for landlord roles
            $landlordRole = Role::on(config('tenancy.database.central_connection'))
                ->where('name', $role)
                ->where('guard_name', 'landlord')
                ->where('tenant_id', 'skolaris-root')
                ->first();

            if (!$landlordRole) {
                $landlordRole = Role::on(config('tenancy.database.central_connection'))->create([
                    'name' => $role,
                    'guard_name' => 'landlord',
                    'tenant_id' => 'skolaris-root', // Landlord roles use root tenant
                ]);
            }

            // Assign role
            $user->assignRole($landlordRole);

            // Create or get permission
            $permission = \Spatie\Permission\Models\Permission::on(config('tenancy.database.central_connection'))
                ->where('name', 'access landlord dashboard')
                ->where('guard_name', 'landlord')
                ->where('tenant_id', 'skolaris-root')
                ->first();

            if (!$permission) {
                $permission = \Spatie\Permission\Models\Permission::on(config('tenancy.database.central_connection'))->create([
                    'name' => 'access landlord dashboard',
                    'guard_name' => 'landlord',
                    'tenant_id' => 'skolaris-root', // Landlord permissions use root tenant
                ]);
            }

            // Give landlord dashboard permission
            $user->givePermissionTo($permission);

            $this->newLine();
            $this->info('✅ Landlord user created successfully!');
            $this->newLine();
            $this->table(
                ['Field', 'Value'],
                [
                    ['ID', $user->id],
                    ['Name', $user->name],
                    ['Email', $user->email],
                    ['Role', $role],
                    ['Created', $user->created_at->toDateTimeString()],
                ]
            );
            $this->newLine();
            $this->info('🔐 Login at: ' . url('/landlord/login'));
            $this->info('📧 Email: ' . $user->email);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Failed to create user: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
