<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

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
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.tenant.database' => ':memory:',

            // Tenancy package configuration
            'tenancy.database.template_connection' => 'sqlite',
            'tenancy.database.tenant_connection' => 'sqlite',
        ]);

        return $app;
    }
}
