<?php

return [
    'telemetry' => [
        'enabled' => (bool) env('INTEGRATION_TELEMETRY_ENABLED', false),
        'base_url' => env('INTEGRATION_TELEMETRY_URL'),
        'token' => env('INTEGRATION_TELEMETRY_TOKEN'),
        'timeout' => (int) env('INTEGRATION_TELEMETRY_TIMEOUT', 5),
        'cache_ttl' => (int) env('INTEGRATION_TELEMETRY_CACHE_TTL', 60),
    ],
];
