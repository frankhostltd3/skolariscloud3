<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Central Domains
    |--------------------------------------------------------------------------
    |
    | These are the domains that are considered "central" or "landlord" domains.
    | Requests to these domains will NOT trigger tenant database connections.
    | The landing page, login, register, and landlord panel are served here.
    |
    | The CENTRAL_DOMAIN environment variable is the primary domain.
    | Additional domains can be added to the array below.
    |
    */

    'central_domains' => array_filter([
        env('CENTRAL_DOMAIN', 'localhost'),
        'www.' . env('CENTRAL_DOMAIN', 'localhost'),
    ]),

    /*
    |--------------------------------------------------------------------------
    | Central Database Connection
    |--------------------------------------------------------------------------
    |
    | The database connection name used for central/landlord data.
    | This is where schools, billing plans, and landlord users are stored.
    |
    */

    'central_connection' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Tenant Database Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix used for tenant database names.
    | Example: "tenant_" results in databases like "tenant_000001"
    |
    */

    'database_prefix' => env('TENANT_DB_PREFIX', 'tenant_'),

    /*
    |--------------------------------------------------------------------------
    | Tenant Database Charset & Collation
    |--------------------------------------------------------------------------
    |
    | Default charset and collation for tenant databases.
    |
    */

    'charset' => env('TENANT_DB_CHARSET', 'utf8mb4'),
    'collation' => env('TENANT_DB_COLLATION', 'utf8mb4_unicode_ci'),

];
