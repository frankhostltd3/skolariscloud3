<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TestTenantConnection extends Command
{
    protected $signature = 'tenant:test-connection {subdomain}';

    protected $description = 'Test tenant database connection for a subdomain';

    public function __construct(private TenantDatabaseManager $tenants)
    {
        parent::__construct();
    }

    public function handle()
    {
        $subdomain = $this->argument('subdomain');

        $this->info("Testing connection for subdomain: {$subdomain}");

        // Find school
        $centralConnection = config('database.central_connection', config('database.default'));
        $school = School::on($centralConnection)
            ->where('subdomain', $subdomain)
            ->first();

        if (!$school) {
            $this->error("School with subdomain '{$subdomain}' not found!");
            return 1;
        }

        $this->info("Found school: {$school->name}");
        $this->info("Database: {$school->database}");

        // Connect
        $this->tenants->connect($school);

        // Check configuration
        $configDb = Config::get('database.connections.tenant.database');
        $this->info("Configured tenant database: {$configDb}");

        $defaultConnection = Config::get('database.default');
        $this->info("Default connection: {$defaultConnection}");

        // Try to query users
        try {
            $userCount = DB::connection('tenant')->table('users')->count();
            $this->info("✅ Successfully connected! User count: {$userCount}");

            // Test User model
            $users = \App\Models\User::take(3)->get(['id', 'name', 'email']);
            $this->info("\nFirst 3 users:");
            foreach ($users as $user) {
                $this->line("  - #{$user->id}: {$user->name} ({$user->email})");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Connection failed: " . $e->getMessage());
            return 1;
        } finally {
            $this->tenants->disconnect();
        }
    }
}
