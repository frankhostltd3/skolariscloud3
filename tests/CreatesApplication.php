<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Force all database connections to use in-memory SQLite databases for testing
        config([
            'database.default' => 'sqlite',
            'database.central_connection' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.tenant.driver' => 'sqlite',
            'database.connections.tenant.database' => ':memory:',

            // Tenancy package configuration
            'tenancy.database.central_connection' => 'sqlite',
            'tenancy.database.template_connection' => 'sqlite',
            'tenancy.database.tenant_connection' => 'sqlite',
        ]);

        DB::setDefaultConnection('sqlite');

        return $app;
    }
}
