<?php

return [
    'payment_gateways' => [
        'flutterwave' => [
            'public_key' => env('FLW_PUBLIC_KEY'),
            'secret_key' => env('FLW_SECRET_KEY'),
            'encryption_key' => env('FLW_ENCRYPTION_KEY'),
        ],
        'stripe' => [
            'api_key' => env('STRIPE_API_KEY'),
            'api_secret' => env('STRIPE_API_SECRET'),
        ],
        'paypal' => [
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
            'mode' => env('PAYPAL_MODE', 'sandbox'),
        ],
    ],
    'notifications' => [
        'sms' => [
            'provider' => env('SMS_PROVIDER', 'twilio'),
            // Add other providers config here
        ],
    ],
];
