<?php

namespace App\Console\Commands;

use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixPermissionMigrations extends Command
{
    protected $signature = 'tenants:fix-permission-migrations';
    protected $description = 'Remove old permission migration records and re-run migrations';

    public function handle(): int
    {
        $schools = School::all();

        foreach ($schools as $school) {
            $dbName = 'tenant_' . str_pad($school->id, 6, '0', STR_PAD_LEFT);

            $this->info("Fixing {$school->name} ({$dbName})...");

            try {
                // Delete old permission migration records
                DB::statement("DELETE FROM {$dbName}.migrations WHERE migration LIKE '%permission%'");
                $this->line("  ✓ Deleted old permission migration records");

                // Check if tables exist
                $tables = DB::select("SHOW TABLES FROM {$dbName} LIKE '%permission%' OR LIKE '%role%'");

                if (count($tables) > 0) {
                    $this->line("  ✓ Permission tables already exist");
                }
            } catch (\Exception $e) {
                $this->error("  ✗ Error: " . $e->getMessage());
            }
        }

        $this->info("\nNow run: php artisan tenants:migrate");
        return self::SUCCESS;
    }
}
