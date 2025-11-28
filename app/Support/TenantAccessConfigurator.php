<?php

namespace App\Support;

use Database\Seeders\PermissionsSeeder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\PermissionRegistrar;

class TenantAccessConfigurator
{
    /**
     * Create a new configurator instance.
     */
    public function __construct(private Application $app)
    {
    }

    /**
     * Ensure tenant roles, permissions, and optional sample data are provisioned.
     */
    public function seed(bool $includeSampleUsers = false): void
    {
        $this->withTeamContext(function () use ($includeSampleUsers) {
            $this->ensureBasePermissions();

            if ($includeSampleUsers) {
                $this->seedSampleUsers();
            }
        });
    }

    /**
     * Execute the given callback with the current tenant id registered as the permission team.
     */
    public function withTeamContext(callable $callback, int|string|null $teamId = null): mixed
    {
        $registrar = $this->app->make(PermissionRegistrar::class);
        $previousTeamId = $registrar->getPermissionsTeamId();
        $tenantId = $teamId ?? $this->currentTenantKey();

        if ($tenantId === null) {
            Log::warning('TenantAccessConfigurator: missing tenant id for team context');

            return $callback();
        }

        $registrar->setPermissionsTeamId($tenantId);

        try {
            return $callback();
        } finally {
            $registrar->setPermissionsTeamId($previousTeamId);
        }
    }

    /**
     * Seed the default permission + role set when none exist yet.
     */
    private function ensureBasePermissions(): void
    {
        $roleModel = config('permission.models.role');

        if (! is_string($roleModel) || ! class_exists($roleModel) || ! is_subclass_of($roleModel, Model::class)) {
            Log::warning('permission.models.role is unavailable; skipping tenant access configuration.');

            return;
        }

        if ($roleModel::query()->where('guard_name', 'web')->exists()) {
            return; // Already seeded for this tenant.
        }

        $this->runSeeder(PermissionsSeeder::class);
    }

    /**
     * Seed optional sample users when the seeder is available.
     */
    private function seedSampleUsers(): void
    {
        $sampleSeeder = '\\Database\\Seeders\\TenantSampleUsersSeeder';

        if (! class_exists($sampleSeeder)) {
            return;
        }

        $this->runSeeder($sampleSeeder);
    }

    /**
     * Resolve and execute a Laravel database seeder class.
     */
    private function runSeeder(string $class): void
    {
        if (! class_exists($class)) {
            Log::warning('Attempted to run missing seeder', ['seeder' => $class]);

            return;
        }

        $seeder = $this->app->make($class);

        if (method_exists($seeder, 'setContainer')) {
            $seeder->setContainer($this->app);
        }

        $seeder();
    }

    private function currentTenantKey(): int|string|null
    {
        if (function_exists('tenant')) {
            $tenant = tenant();

            if (is_object($tenant)) {
                if (method_exists($tenant, 'getTenantKey')) {
                    return $tenant->getTenantKey();
                }

                if (method_exists($tenant, 'getKey')) {
                    return $tenant->getKey();
                }

                if (property_exists($tenant, 'id')) {
                    return $tenant->id;
                }
            }
        }

        return null;
    }
}
