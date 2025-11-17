<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestLogin extends Command
{
    protected $signature = 'test:login {email}';
    protected $description = 'Test if a user can login';

    public function handle()
    {
        $email = $this->argument('email');

        $this->info('Searching for user: ' . $email);

        // Get all schools
        $schools = School::all();
        $this->info('Found ' . $schools->count() . ' schools');

        foreach ($schools as $school) {
            $this->info("\nChecking school: {$school->name} (ID: {$school->id})");
            $this->info("Database: {$school->database}");

            try {
                // Connect to tenant database
                config(['database.connections.tenant.database' => $school->database]);
                DB::reconnect('tenant');

                // Check if user exists
                $user = DB::connection('tenant')
                    ->table('users')
                    ->where('email', $email)
                    ->first();

                if ($user) {
                    $this->info("✓ User found!");
                    $this->info("  Name: {$user->name}");
                    $this->info("  Email: {$user->email}");
                    $this->info("  School ID: {$user->school_id}");
                    $this->info("  User Type: {$user->user_type}");

                    // Check if password is hashed
                    $this->info("  Password starts with: " . substr($user->password, 0, 10));

                    return Command::SUCCESS;
                }

            } catch (\Exception $e) {
                $this->error("Error checking {$school->name}: " . $e->getMessage());
            }
        }

        $this->error("\n✗ User not found in any tenant database");
        return Command::FAILURE;
    }
}
