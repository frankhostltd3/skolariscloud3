<?php

return [
    'payment_gateways' => [
        'flutterwave' => [
            'public_key' => env('FLUTTERWAVE_PUBLIC_KEY'),
            'secret_key' => env('FLUTTERWAVE_SECRET_KEY'),
            'encryption_key' => env('FLUTTERWAVE_ENCRYPTION_KEY'),
        ],
        'stripe' => [
            'api_key' => env('STRIPE_PUBLISHABLE_KEY'),
            'api_secret' => env('STRIPE_SECRET_KEY'),
        ],
        'paypal' => [
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_CLIENT_SECRET'),
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
