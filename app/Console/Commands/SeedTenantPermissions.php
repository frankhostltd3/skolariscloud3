<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Console\Command;

class SeedTenantPermissions extends Command
{
    protected $signature = 'tenants:seed-permissions {--force : Force seeding even if permissions exist}';

    protected $description = 'Seed permissions and roles for all tenant databases';

    public function __construct(private TenantDatabaseManager $manager)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $schools = School::query()->orderBy('id')->get();

        if ($schools->isEmpty()) {
            $this->components->info('No schools found.');
            return self::SUCCESS;
        }

        foreach ($schools as $school) {
            $this->components->task("Seeding permissions for {$school->name}", function () use ($school, $force) {
                $this->manager->runFor(
                    $school,
                    function () use ($force) {
                        // Check if permissions already exist
                        $permissionCount = \Spatie\Permission\Models\Permission::count();

                        if ($permissionCount > 0 && !$force) {
                            $this->line("  Permissions already exist ({$permissionCount} permissions). Use --force to reseed.");
                            return;
                        }

                        if ($force && $permissionCount > 0) {
                            // Clear existing permissions and roles
                            \Spatie\Permission\Models\Permission::query()->delete();
                            \Spatie\Permission\Models\Role::query()->delete();
                            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                        }

                        // Run the seeder
                        $this->call('db:seed', [
                            '--class' => 'PermissionsSeeder',
                            '--force' => true,
                        ]);
                    }
                );
            });
        }

        $this->components->info('Permissions seeding completed for all tenants!');
        return self::SUCCESS;
    }
}
