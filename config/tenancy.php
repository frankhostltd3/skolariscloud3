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
];
