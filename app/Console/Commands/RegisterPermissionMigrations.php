<?php

namespace App\Console\Commands;

use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegisterPermissionMigrations extends Command
{
    protected $signature = 'tenants:register-permission-migrations';
    protected $description = 'Register permission migration as completed without running it (tables already exist)';

    public function handle(): int
    {
        $schools = School::all();
        $migrationName = '2025_11_15_195530_create_permission_tables';
        $batch = 1;

        foreach ($schools as $school) {
            $dbName = 'tenant_' . str_pad($school->id, 6, '0', STR_PAD_LEFT);

            $this->info("Registering migration for {$school->name} ({$dbName})...");

            try {
                // Check if tables exist
                $tablesExist = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '{$dbName}' AND table_name IN ('permissions', 'roles')")[0]->count;

                if ($tablesExist < 2) {
                    $this->warn("  ⚠ Permission tables don't exist in this database! Run migrations first.");
                    continue;
                }

                // Check if migration record exists
                $exists = DB::select("SELECT COUNT(*) as count FROM {$dbName}.migrations WHERE migration = '{$migrationName}'")[0]->count;

                if ($exists > 0) {
                    $this->line("  ✓ Migration already registered");
                    continue;
                }

                // Get highest batch number
                $maxBatch = DB::select("SELECT COALESCE(MAX(batch), 0) as max FROM {$dbName}.migrations")[0]->max;
                $batch = $maxBatch + 1;

                // Insert migration record
                DB::statement("INSERT INTO {$dbName}.migrations (migration, batch) VALUES ('{$migrationName}', {$batch})");
                $this->info("  ✓ Migration registered successfully (batch {$batch})");

            } catch (\Exception $e) {
                $this->error("  ✗ Error: " . $e->getMessage());
            }
        }

        $this->info("\nDone! Now seed permissions with: php artisan tenants:seed-permissions");
        return self::SUCCESS;
    }
}
