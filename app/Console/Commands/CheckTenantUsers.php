<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTenantUsers extends Command
{
    protected $signature = 'tenant:check-users {school_id}';

    protected $description = 'Check users in a tenant database';

    public function __construct(private TenantDatabaseManager $tenants)
    {
        parent::__construct();
    }

    public function handle()
    {
        $schoolId = $this->argument('school_id');
        $school = School::find($schoolId);

        if (!$school) {
            $this->error("School with ID {$schoolId} not found!");
            return 1;
        }

        $this->info("Checking users for: {$school->name} ({$school->subdomain})");
        $this->info("Database: {$school->database}");

        $this->tenants->connect($school);

        try {
            $users = DB::connection('tenant')->table('users')
                ->select('id', 'name', 'email', 'user_type', 'created_at')
                ->get();

            if ($users->isEmpty()) {
                $this->warn('No users found!');
            } else {
                $this->info("\nUsers in {$school->database}:");
                $this->table(
                    ['ID', 'Name', 'Email', 'Type', 'Created At'],
                    $users->map(fn($u) => [$u->id, $u->name, $u->email, $u->user_type, $u->created_at])
                );

                $this->info("\nTotal users: " . $users->count());
            }
        } finally {
            $this->tenants->disconnect();
        }

        return 0;
    }
}
