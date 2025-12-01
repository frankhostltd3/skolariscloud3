<?php

return [
    App\Providers\TenantConnectionProvider::class, // Must be FIRST - sets up tenant DB connection
    App\Providers\AppServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\HealthCheckServiceProvider::class,
];
