<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Central Domain
    |--------------------------------------------------------------------------
    |
    | This value represents the primary domain that hosts the marketing site
    | and onboarding flow. You can override it in your .env file using the
    | CENTRAL_DOMAIN variable. When left unset, the host portion of APP_URL
    | will be used as a fallback.
    |
    */
    'central_domain' => env('CENTRAL_DOMAIN', parse_url(config('app.url'), PHP_URL_HOST) ?: 'smatcampus.test'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | These options allow the landlord (central) features to explicitly select
    | which database connections should be used for central, template, and
    | tenant operations. Providing them here keeps all multi-tenant connection
    | configuration in a single place and lets the test suite override them.
    |
    */
    'database' => [
        'central_connection' => env(
            'TENANCY_DATABASE_CENTRAL_CONNECTION',
            env('CENTRAL_DB_CONNECTION', env('DB_CONNECTION', 'mysql'))
        ),
        'tenant_connection' => env(
            'TENANCY_DATABASE_TENANT_CONNECTION',
            env('TENANT_DB_CONNECTION', env('DB_CONNECTION', 'mysql'))
        ),
        'template_connection' => env(
            'TENANCY_DATABASE_TEMPLATE_CONNECTION',
            env('TENANT_TEMPLATE_CONNECTION', env('DB_CONNECTION', 'mysql'))
        ),
    ],
];
