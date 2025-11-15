<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Console\Command;

class SeedTenantCurrencies extends Command
{
    protected $signature = 'tenants:seed-currencies';

    protected $description = 'Seed currencies for all tenant databases.';

    public function __construct(private TenantDatabaseManager $manager)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $schools = School::query()->orderBy('id')->get();

        if ($schools->isEmpty()) {
            $this->components->info('No schools found.');

            return self::SUCCESS;
        }

        foreach ($schools as $school) {
            $this->components->task("Seeding currencies for {$school->name}", function () use ($school) {
                $this->manager->runFor(
                    $school,
                    function () {
                        \Artisan::call('db:seed', ['--class' => 'CurrencySeeder']);
                    },
                    runMigrations: false
                );
            });
        }

        return self::SUCCESS;
    }
}
