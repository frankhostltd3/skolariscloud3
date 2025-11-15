<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Console\Command;

class MigrateTenants extends Command
{
    protected $signature = 'tenants:migrate {--fresh : Drop all tables and re-run tenant migrations} {--seed : Seed the tenant database after migrating}';

    protected $description = 'Run tenant database migrations for every school tenant.';

    public function __construct(private TenantDatabaseManager $manager)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $fresh = (bool) $this->option('fresh');
        $seed = (bool) $this->option('seed');

        $schools = School::query()->orderBy('id')->get();

        if ($schools->isEmpty()) {
            $this->components->info('No schools found.');

            return self::SUCCESS;
        }

        foreach ($schools as $school) {
            $this->components->task("Migrating tenant database for {$school->name}", function () use ($school, $fresh, $seed) {
                $this->manager->runFor(
                    $school,
                    fn () => null,
                    runMigrations: true,
                    fresh: $fresh,
                    seed: $seed
                );
            });
        }

        return self::SUCCESS;
    }
}
