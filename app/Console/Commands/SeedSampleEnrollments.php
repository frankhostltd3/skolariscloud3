<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Database\Seeders\SampleEnrollmentSeeder;
use Illuminate\Console\Command;

class SeedSampleEnrollments extends Command
{
    protected $signature = 'tenants:seed-enrollments {--school=}';

    protected $description = 'Seed demo enrollment records for one or all tenant schools.';

    public function __construct(private TenantDatabaseManager $manager)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $query = School::query()->orderBy('id');

        if ($target = $this->option('school')) {
            $query->where(function ($builder) use ($target) {
                $builder->where('code', $target)->orWhere('id', $target);
            });
        }

        $schools = $query->get();

        if ($schools->isEmpty()) {
            $this->components->warn('No matching schools found.');

            return self::SUCCESS;
        }

        foreach ($schools as $school) {
            $this->components->task("Seeding enrollments for {$school->name}", function () use ($school) {
                $this->manager->runFor(
                    $school,
                    function () {
                        \Artisan::call('db:seed', [
                            '--class' => SampleEnrollmentSeeder::class,
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
