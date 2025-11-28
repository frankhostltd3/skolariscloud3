<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\School;
use Database\Seeders\AcademicYearSeeder;
use App\Services\TenantDatabaseManager;

class SeedAcademicYears extends Command
{
    protected $signature = 'tenants:seed-academic-years';
    protected $description = 'Seed academic years for all tenants';

    public function handle(TenantDatabaseManager $manager)
    {
        School::all()->each(function($school) use ($manager) {
            $this->info("Seeding {$school->name}...");

            $manager->runFor($school, function() {
                $seeder = new AcademicYearSeeder();
                $seeder->run();
            });
        });
    }
}
