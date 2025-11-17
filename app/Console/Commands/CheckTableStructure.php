<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTableStructure extends Command
{
    protected $signature = 'tenant:check-structure {school_id} {table}';

    protected $description = 'Check table structure in a tenant database';

    public function __construct(private TenantDatabaseManager $tenants)
    {
        parent::__construct();
    }

    public function handle()
    {
        $schoolId = $this->argument('school_id');
        $table = $this->argument('table');
        $school = School::find($schoolId);

        if (!$school) {
            $this->error("School with ID {$schoolId} not found!");
            return 1;
        }

        $this->info("Checking table structure for: {$school->name} ({$school->subdomain})");
        $this->info("Database: {$school->database}");
        $this->info("Table: {$table}");

        $this->tenants->connect($school);

        try {
            $columns = DB::connection('tenant')->select("SHOW COLUMNS FROM {$table}");

            $this->info("\nColumns in {$table}:");
            $this->table(
                ['Field', 'Type', 'Null', 'Key', 'Default', 'Extra'],
                collect($columns)->map(fn($c) => [
                    $c->Field,
                    $c->Type,
                    $c->Null,
                    $c->Key,
                    $c->Default,
                    $c->Extra
                ])
            );
        } finally {
            $this->tenants->disconnect();
        }

        return 0;
    }
}
