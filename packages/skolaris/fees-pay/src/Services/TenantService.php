<?php

namespace Skolaris\FeesPay\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class TenantService
{
    public function createTenantDatabase(string $databaseName)
    {
        // Create the database
        DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Run migrations for the new tenant
        $this->switchToTenant($databaseName);
        $this->runMigrations();
    }

    public function switchToTenant(string $databaseName)
    {
        Config::set('database.connections.tenant', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $databaseName,
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);

        DB::purge('tenant');
        DB::reconnect('tenant');
        Schema::connection('tenant')->getConnection()->reconnect();
    }

    protected function runMigrations()
    {
        // Run migrations on the 'tenant' connection
        \Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant', // Assuming tenant migrations are here
            '--force' => true,
        ]);
    }
}
