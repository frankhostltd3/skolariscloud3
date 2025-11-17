<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTenantTables extends Command
{
    protected $signature = 'tenant:check-tables {school_id}';

    protected $description = 'Check tables in a tenant database';

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

        $this->info("Checking database for: {$school->name} ({$school->subdomain})");
        $this->info("Database: {$school->database}");

        $this->tenants->connect($school);

        try {
            $tables = DB::connection('tenant')->select('SHOW TABLES');

            if (empty($tables)) {
                $this->warn('No tables found! Migrations may not have run.');
            } else {
                $this->info("\nTables in {$school->database}:");
                foreach ($tables as $table) {
                    $tableArray = (array) $table;
                    $tableName = reset($tableArray);
                    $this->line('  - ' . $tableName);
                }

                $this->info("\nTotal tables: " . count($tables));
            }
        } finally {
            $this->tenants->disconnect();
        }

        return 0;
    }
}
