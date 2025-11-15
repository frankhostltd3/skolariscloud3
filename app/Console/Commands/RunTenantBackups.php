<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\School;

class RunTenantBackups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:backup {frequency : daily, weekly, or monthly}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run backups for all tenants based on their auto_backup settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $frequency = $this->argument('frequency');

        $this->info("Running {$frequency} backups for all tenants...");

        // Get all active schools
        $schools = School::where('status', 'active')->get();

        $backedUp = 0;
        $skipped = 0;

        foreach ($schools as $school) {
            // Switch to tenant database
            config(['database.connections.tenant.database' => $school->database_name]);
            DB::purge('tenant');
            DB::reconnect('tenant');

            // Check if backup is enabled for this tenant
            $autoBackup = $this->getTenantSetting($school, 'auto_backup', 'disabled');

            if ($autoBackup === $frequency) {
                $this->info("  - Backing up {$school->name} ({$school->subdomain})...");

                try {
                    // Update backup config for this tenant
                    config([
                        'backup.backup.name' => $school->subdomain,
                        'backup.backup.source.databases' => ['tenant'],
                        'backup.cleanup.default_strategy.keep_all_backups_for_days' =>
                            (int) $this->getTenantSetting($school, 'backup_retention', 30),
                    ]);

                    // Run backup for this tenant
                    Artisan::call('backup:run', [
                        '--only-db' => true,
                        '--disable-notifications' => true,
                    ]);

                    $this->line("    ✓ Backup completed for {$school->name}");
                    $backedUp++;

                } catch (\Exception $e) {
                    $this->error("    ✗ Backup failed for {$school->name}: {$e->getMessage()}");
                }
            } else {
                $skipped++;
            }
        }

        $this->newLine();
        $this->info("Backup Summary:");
        $this->line("  - Backed up: {$backedUp} tenants");
        $this->line("  - Skipped: {$skipped} tenants");

        return Command::SUCCESS;
    }

    /**
     * Get setting value for a specific tenant
     */
    private function getTenantSetting($school, $key, $default = null)
    {
        try {
            $setting = DB::connection('tenant')
                ->table('settings')
                ->where('key', $key)
                ->first();

            return $setting ? $setting->value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}
