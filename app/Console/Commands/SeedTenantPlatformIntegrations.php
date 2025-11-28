<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Console\Command;

class SeedTenantPlatformIntegrations extends Command
{
    protected $signature = 'tenants:seed-integrations {--school=* : Limit the operation to specific school IDs}';

    protected $description = 'Ensure every tenant database has baseline platform integration records.';

    public function __construct(private TenantDatabaseManager $manager)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $query = School::query()->orderBy('id');

        if ($this->option('school')) {
            $ids = array_filter(array_map('intval', (array) $this->option('school')));
            if (! empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        $schools = $query->get();

        if ($schools->isEmpty()) {
            $this->components->info('No schools found.');

            return self::SUCCESS;
        }

        foreach ($schools as $school) {
            $this->components->task("Seeding platform integrations for {$school->name}", function () use ($school) {
                $this->manager->runFor(
                    $school,
                    function () {
                        \Artisan::call('db:seed', [
                            '--class' => 'PlatformIntegrationSeeder',
                            '--force' => true,
                        ]);
                    },
                    runMigrations: false
                );
            });
        }

        return self::SUCCESS;
    }
}
