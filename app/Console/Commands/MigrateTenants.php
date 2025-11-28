<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Console\Command;

class MigrateTenants extends Command
{
    protected $signature = 'tenants:migrate {--fresh : Drop all tables and re-run tenant migrations} {--seed : Seed the tenant database after migrating} {--path= : Run only the specified tenant migration file or directory}';

    protected $description = 'Run tenant database migrations for every school tenant.';

    public function __construct(private TenantDatabaseManager $manager)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $fresh = (bool) $this->option('fresh');
        $seed = (bool) $this->option('seed');
        $path = $this->option('path');

        $schools = School::query()->orderBy('id')->get();

        if ($schools->isEmpty()) {
            $this->components->info('No schools found.');

            return self::SUCCESS;
        }

        $this->components->info("Found {$schools->count()} school(s) to migrate.");
        $this->newLine();

        $results = [
            'success' => [],
            'failed' => [],
            'skipped' => [],
        ];

        foreach ($schools as $school) {
            try {
                $this->components->task("Migrating tenant database for {$school->name}", function () use ($school, $fresh, $seed, $path) {
                    $this->manager->runFor(
                        $school,
                        fn () => null,
                        runMigrations: true,
                        fresh: $fresh,
                        seed: $seed,
                        path: $path
                    );
                });

                $results['success'][] = $school->name;
            } catch (\Exception $e) {
                $this->components->error("Failed to migrate {$school->name}: " . $e->getMessage());
                $results['failed'][] = [
                    'name' => $school->name,
                    'error' => $e->getMessage(),
                ];

                // Continue with next school instead of stopping
                continue;
            }
        }

        // Display summary
        $this->newLine();
        $this->components->info('=== Migration Summary ===');

        if (!empty($results['success'])) {
            $this->components->info("✓ Successfully migrated " . count($results['success']) . " school(s):");
            foreach ($results['success'] as $name) {
                $this->line("  - {$name}");
            }
        }

        if (!empty($results['failed'])) {
            $this->newLine();
            $this->components->warn("✗ Failed to migrate " . count($results['failed']) . " school(s):");
            foreach ($results['failed'] as $failure) {
                $this->line("  - {$failure['name']}: {$failure['error']}");
            }
        }

        return empty($results['failed']) ? self::SUCCESS : self::FAILURE;
    }
}
