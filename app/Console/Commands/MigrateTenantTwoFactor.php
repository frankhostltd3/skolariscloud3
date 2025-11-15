<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateTenantTwoFactor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate-twofactor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add two-factor authentication columns to users table in all tenant databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantManager = app(TenantDatabaseManager::class);
        $schools = School::all();

        if ($schools->isEmpty()) {
            $this->error('No schools found.');
            return 1;
        }

        $this->info('Adding 2FA columns to users table in all tenant databases...');

        foreach ($schools as $school) {
            $this->line("Processing: {$school->name} (Database: {$school->database})");

            try {
                // Switch to tenant database
                $tenantManager->connect($school);

                // Check if columns already exist
                if (Schema::connection('tenant')->hasColumn('users', 'two_factor_secret')) {
                    $this->warn("  → 2FA columns already exist. Skipping.");
                    continue;
                }

                // Add columns
                DB::connection('tenant')->statement('
                    ALTER TABLE users
                    ADD COLUMN two_factor_secret TEXT NULL AFTER password,
                    ADD COLUMN two_factor_recovery_codes TEXT NULL AFTER two_factor_secret,
                    ADD COLUMN two_factor_confirmed_at TIMESTAMP NULL AFTER two_factor_recovery_codes
                ');

                $this->info("  ✓ Successfully added 2FA columns");
            } catch (\Exception $e) {
                $this->error("  ✗ Failed: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info('✓ Migration complete!');

        return 0;
    }
}
