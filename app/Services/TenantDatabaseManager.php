<?php

namespace App\Services;

use App\Models\School;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class TenantDatabaseManager
{
    protected string $centralConnection;

    protected bool $usingTenant = false;

    protected ?int $currentSchoolId = null;

    protected int $tenantDepth = 0;

    public function __construct(private ConfigRepository $config)
    {
        $this->centralConnection = (string) $this->config->get('database.default', env('DB_CONNECTION', 'mysql'));
    }

    public function connect(?School $school): void
    {
        if (! $school instanceof School) {
            $this->useCentral();
            $this->usingTenant = false;
            $this->currentSchoolId = null;
            $this->tenantDepth = 0;

            return;
        }

        $database = $this->prepareDatabaseName($school);

        if ($this->usingTenant && $this->currentSchoolId === $school->id) {
            $this->tenantDepth++;

            return;
        }

        if ($this->usingTenant && $this->currentSchoolId !== $school->id) {
            $this->disconnect();
        }

        $tenantConfig = $this->config->get('database.connections.tenant');

        if (! is_array($tenantConfig)) {
            throw new RuntimeException('Tenant database connection is not configured.');
        }

        $tenantConfig['database'] = $database;

        $this->config->set('database.connections.tenant', $tenantConfig);

        $this->ensureDatabaseExists($database, $tenantConfig['driver'] ?? 'mysql', $tenantConfig);

        // Purge and reconnect
        DB::purge('tenant');

        // Explicitly set the database on the connection config again after purge
        Config::set('database.connections.tenant.database', $database);
        Config::set('database.default', 'tenant');
        DB::setDefaultConnection('tenant');

        $this->usingTenant = true;
        $this->currentSchoolId = $school->id;
        $this->tenantDepth = 1;
    }

    public function disconnect(): void
    {
        if (! $this->usingTenant) {
            return;
        }

        if ($this->tenantDepth > 1) {
            $this->tenantDepth--;

            return;
        }

        DB::purge('tenant');
        $this->useCentral();
        $this->usingTenant = false;
        $this->currentSchoolId = null;
        $this->tenantDepth = 0;
    }

    public function runFor(School $school, callable $callback, bool $runMigrations = false, bool $fresh = false, bool $seed = false)
    {
        $this->connect($school);

        try {
            if ($runMigrations) {
                $this->migrate($fresh, $seed);
            }

            return $callback();
        } finally {
            $this->disconnect();
        }
    }

    public function migrate(bool $fresh = false, bool $seed = false): void
    {
        $command = $fresh ? 'migrate:fresh' : 'migrate';

        $parameters = [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenants',
            '--force' => true,
        ];

        if ($fresh) {
            $parameters['--drop-views'] = true;
            $parameters['--drop-types'] = true;
        }

        Artisan::call($command, $parameters);

        if ($seed) {
            Artisan::call('db:seed', [
                '--database' => 'tenant',
                '--force' => true,
            ]);
        }
    }

    protected function useCentral(): void
    {
        Config::set('database.default', $this->centralConnection);
        DB::setDefaultConnection($this->centralConnection);
    }

    protected function prepareDatabaseName(School $school): string
    {
        if (! empty($school->database)) {
            return $school->database;
        }

        $database = 'tenant_' . Str::padLeft((string) $school->id, 6, '0');

        $school->forceFill(['database' => $database])->save();

        return $database;
    }

    protected function ensureDatabaseExists(string $database, string $driver, array $baseConfig): void
    {
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        $charset = $baseConfig['charset'] ?? 'utf8mb4';
        $collation = $baseConfig['collation'] ?? 'utf8mb4_unicode_ci';

        $escapedDatabase = str_replace('`', '``', $database);

        DB::connection($this->centralConnection)->statement(
            "CREATE DATABASE IF NOT EXISTS `{$escapedDatabase}` CHARACTER SET {$charset} COLLATE {$collation}"
        );
    }
}
