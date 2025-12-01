<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => env('MAILGUN_SCHEME', 'https'),
    ],

    'postmark' => [
        'key' => env('POSTMARK_TOKEN'),
        'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
    ],

    'sendgrid' => [
        'api_key' => env('SENDGRID_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'exam_generation' => [
        'driver' => env('EXAM_GENERATION_DRIVER'),
        'providers' => [
            'openai' => [
                'api_key' => env('OPENAI_API_KEY'),
                'model' => env('OPENAI_EXAM_MODEL', 'gpt-4o-mini'),
            ],
            'azure_openai' => [
                'endpoint' => env('AZURE_OPENAI_ENDPOINT'),
                'deployment' => env('AZURE_OPENAI_DEPLOYMENT'),
                'api_key' => env('AZURE_OPENAI_API_KEY'),
            ],
        ],
    ],


        // FRANKHOST White Label Domain Registrar
        'domain_registrar_1' => [
            'api_key' => env('DOMAIN_REGISTRAR_1_API_KEY'),
            'api_user' => env('DOMAIN_REGISTRAR_1_API_USER'),
            'sandbox' => env('DOMAIN_REGISTRAR_1_SANDBOX', true),
        ],
        'domain_registrar_2' => [
            'api_key' => env('DOMAIN_REGISTRAR_2_API_KEY'),
            'api_user' => env('DOMAIN_REGISTRAR_2_API_USER'),
            'sandbox' => env('DOMAIN_REGISTRAR_2_SANDBOX', true),
        ],

        // FRANKHOST White Label Hosting Providers
        'hosting_provider_1' => [
            'api_key' => env('HOSTING_PROVIDER_1_API_KEY'),
            'api_user' => env('HOSTING_PROVIDER_1_API_USER'),
        ],
        'hosting_provider_2' => [
            'api_key' => env('HOSTING_PROVIDER_2_API_KEY'),
            'api_user' => env('HOSTING_PROVIDER_2_API_USER'),
        ],

        'spaceship' => [
            'api_key' => env('SPACESHIP_API_KEY'),
            'api_user' => env('SPACESHIP_API_USER'),
            'sandbox' => env('SPACESHIP_SANDBOX', true),
        ],

        'internetbs' => [
            'api_key' => env('INTERNETBS_API_KEY'),
            'api_user' => env('INTERNETBS_API_USER'),
            'sandbox' => env('INTERNETBS_SANDBOX', true),
        ],

        // Cloudflare DNS & SSL
        'cloudflare' => [
            'api_token' => env('CLOUDFLARE_API_TOKEN'),
            'account_id' => env('CLOUDFLARE_ACCOUNT_ID'),
        ],

];
