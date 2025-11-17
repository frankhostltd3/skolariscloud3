<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Stancl\Tenancy\Tenancy;
use App\Support\TenantAccessConfigurator;

class TenantRolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we're seeding within a tenant context.
        /** @var Tenancy $tenancy */
        $tenancy = app(Tenancy::class);
        $activeTenant = tenant();

        if (! $activeTenant) {
            $this->command?->warn('No active tenant. Skipping tenant roles/users seeding.');
            return;
        }

        $includeExamples = (bool) config('skolaris.seed_example_users', false);

        app(TenantAccessConfigurator::class)->seed(includeSampleUsers: $includeExamples);
        // Seed report permissions
        $this->call([
            \Database\Seeders\ReportsPermissionsSeeder::class,
        ]);
        // Seed grading schemes
        $this->call([
            \Database\Seeders\InternationalGradingSchemesSeeder::class,
        ]);

        $this->command?->info(match (true) {
            $includeExamples => 'Tenant roles, permissions, sample users, and grading schemes seeded.',
            default => 'Tenant roles, permissions, and grading schemes seeded.',
        });
    }
}
