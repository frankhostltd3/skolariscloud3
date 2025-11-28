<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedEmployeeIdSettings extends Command
{
    protected $signature = 'tenants:seed-employee-id-settings';

    protected $description = 'Seed default employee ID templates for every tenant database';

    public function handle(): int
    {
        $manager = app(TenantDatabaseManager::class);
        $schools = School::query()->get();

        if ($schools->isEmpty()) {
            $this->warn('No tenant schools found. Nothing to seed.');
            return self::SUCCESS;
        }

        foreach ($schools as $school) {
            $this->info(sprintf('Seeding templates for %s (%s)...', $school->name, $school->subdomain ?? 'no-subdomain'));

            $manager->runFor($school, function () {
                Artisan::call('db:seed', [
                    '--database' => 'tenant',
                    '--class' => 'Database\\Seeders\\EmployeeIdSettingsSeeder',
                    '--force' => true,
                ]);
            });
        }

        $this->info('Employee ID templates seeded for all tenants.');

        return self::SUCCESS;
    }
}