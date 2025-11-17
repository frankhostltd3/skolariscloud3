<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateTenantUser extends Command
{
    protected $signature = 'tenant:create-user {school_id} {email} {password} {--name=Admin User}';
    protected $description = 'Create a user in a tenant database';

    public function handle()
    {
        $schoolId = $this->argument('school_id');
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->option('name');

        $school = School::find($schoolId);

        if (!$school) {
            $this->error("School with ID {$schoolId} not found");
            return Command::FAILURE;
        }

        $this->info("Creating user in: {$school->name}");
        $this->info("Database: {$school->database}");

        try {
            // Connect to tenant database
            config(['database.connections.tenant.database' => $school->database]);
            DB::reconnect('tenant');

            // Check if user already exists
            $existing = DB::connection('tenant')
                ->table('users')
                ->where('email', $email)
                ->first();

            if ($existing) {
                $this->error("User with email {$email} already exists");
                return Command::FAILURE;
            }

            // Create user
            $userId = DB::connection('tenant')->table('users')->insertGetId([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'school_id' => $school->id,
                'user_type' => 'admin',
                'email_verified_at' => now(),
                'approval_status' => 'approved',
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info("✓ User created successfully!");
            $this->info("  ID: {$userId}");
            $this->info("  Name: {$name}");
            $this->info("  Email: {$email}");
            $this->info("  Password: {$password}");
            $this->info("\nLogin URL: http://{$school->subdomain}.localhost:8000/login");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Error creating user: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
