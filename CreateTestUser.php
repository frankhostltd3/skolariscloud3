<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-test {--email=admin@frankhost.us} {--password=password123} {--name=Test Admin} {--type=admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test user for login testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');
        $type = $this->option('type');

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists!");
            return 1;
        }

        // Create or get a school if needed
        $school = null;
        if (in_array($type, ['teacher', 'student', 'parent'])) {
            $school = School::first();
            if (!$school) {
                $school = School::create([
                    'name' => 'Test School',
                    'subdomain' => 'testschool',
                    'status' => 'active'
                ]);
                $this->info("Created test school: {$school->name}");
            }
        }

        // Create user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make($password),
            'user_type' => $type,
            'school_id' => $school ? $school->id : null,
        ]);

        $this->info("Test user created successfully!");
        $this->line("Email: {$email}");
        $this->line("Password: {$password}");
        $this->line("Name: {$name}");
        $this->line("Type: {$type}");
        if ($school) {
            $this->line("School: {$school->name}");
        }

        return 0;
    }
}
