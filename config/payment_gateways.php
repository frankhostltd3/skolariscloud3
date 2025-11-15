<?php

return [
    'gateways' => [
        'paypal' => [
            'label' => 'PayPal',
            'description' => 'Accept payments through PayPal Express and REST APIs.',
            'fields' => [
                'mode' => [
                    'label' => 'Mode',
                    'type' => 'select',
                    'options' => [
                        'sandbox' => 'Sandbox',
                        'live' => 'Live',
                    ],
                    'rules' => ['required_with:gateways.paypal.is_enabled', 'in:sandbox,live'],
                    'default' => 'sandbox',
                    'env' => 'PAYPAL_MODE',
                ],
                'client_id' => [
                    'label' => 'Client ID',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:191'],
                    'env' => 'PAYPAL_CLIENT_ID',
                ],
                'client_secret' => [
                    'label' => 'Client Secret',
                    'type' => 'password',
                    'rules' => ['nullable', 'string', 'max:191'],
                    'env' => 'PAYPAL_CLIENT_SECRET',
                    'conceal' => true,
                ],
                'webhook_id' => [
                    'label' => 'Webhook ID',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:191'],
                    'env' => 'PAYPAL_WEBHOOK_ID',
                ],
            ],
        ],
        'stripe' => [
            'label' => 'Stripe',
            'description' => 'Process card transactions with Stripe.',
            'fields' => [
                'mode' => [
                    'label' => 'Mode',
                    'type' => 'select',
                    'options' => [
                        'test' => 'Test',
                        'live' => 'Live',
                    ],
                    'rules' => ['required_with:gateways.stripe.is_enabled', 'in:test,live'],
                    'default' => 'test',
                    'env' => 'STRIPE_MODE',
                ],
                'publishable_key' => [
                    'label' => 'Publishable Key',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'STRIPE_PUBLISHABLE_KEY',
                ],
                'secret_key' => [
                    'label' => 'Secret Key',
                    'type' => 'password',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'STRIPE_SECRET_KEY',
                    'conceal' => true,
                ],
                'webhook_secret' => [
                    'label' => 'Webhook Signing Secret',
                    'type' => 'password',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'STRIPE_WEBHOOK_SECRET',
                    'conceal' => true,
                ],
            ],
        ],
        'flutterwave' => [
            'label' => 'Flutterwave',
            'description' => 'Support payments across Africa with Flutterwave.',
            'fields' => [
                'environment' => [
                    'label' => 'Environment',
                    'type' => 'select',
                    'options' => [
                        'sandbox' => 'Sandbox',
                        'live' => 'Live',
                    ],
                    'rules' => ['required_with:gateways.flutterwave.is_enabled', 'in:sandbox,live'],
                    'default' => 'sandbox',
                    'env' => 'FLUTTERWAVE_ENV',
                ],
                'public_key' => [
                    'label' => 'Public Key',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'FLUTTERWAVE_PUBLIC_KEY',
                ],
                'secret_key' => [
                    'label' => 'Secret Key',
                    'type' => 'password',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'FLUTTERWAVE_SECRET_KEY',
                    'conceal' => true,
                ],
                'encryption_key' => [
                    'label' => 'Encryption Key',
                    'type' => 'password',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'FLUTTERWAVE_ENCRYPTION_KEY',
                    'conceal' => true,
                ],
                'webhook_secret' => [
                    'label' => 'Webhook Secret',
                    'type' => 'password',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'FLUTTERWAVE_WEBHOOK_SECRET',
                    'conceal' => true,
                ],
            ],
        ],
        'mtn_momo' => [
            'label' => 'MTN MoMo',
            'description' => 'Integrate MTN Mobile Money across supported countries.',
            'fields' => [
                'environment' => [
                    'label' => 'Environment',
                    'type' => 'select',
                    'options' => [
                        'sandbox' => 'Sandbox',
                        'production' => 'Production',
                    ],
                    'rules' => ['required_with:gateways.mtn_momo.is_enabled', 'in:sandbox,production'],
                    'default' => 'sandbox',
                    'env' => 'MTN_MOMO_ENV',
                ],
                'primary_key' => [
                    'label' => 'Primary API Key',
                    'type' => 'password',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'MTN_MOMO_PRIMARY_KEY',
                    'conceal' => true,
                ],
                'secondary_key' => [
                    'label' => 'Secondary API Key',
                    'type' => 'password',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'MTN_MOMO_SECONDARY_KEY',
                    'conceal' => true,
                ],
                'subscription_key' => [
                    'label' => 'Subscription Key',
                    'type' => 'password',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'MTN_MOMO_SUBSCRIPTION_KEY',
                    'conceal' => true,
                ],
                'callback_url' => [
                    'label' => 'Callback URL',
                    'type' => 'url',
                    'rules' => ['nullable', 'url', 'max:255'],
                    'env' => 'MTN_MOMO_CALLBACK_URL',
                ],
                'country_codes' => [
                    'label' => 'Supported Country Codes (comma separated)',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'MTN_MOMO_COUNTRIES',
                ],
            ],
        ],
        'airtel_money' => [
            'label' => 'Airtel Money',
            'description' => 'Handle Airtel Money collections where available.',
            'fields' => [
                'environment' => [
                    'label' => 'Environment',
                    'type' => 'select',
                    'options' => [
                        'sandbox' => 'Sandbox',
                        'production' => 'Production',
                    ],
                    'rules' => ['required_with:gateways.airtel_money.is_enabled', 'in:sandbox,production'],
                    'default' => 'sandbox',
                    'env' => 'AIRTEL_MONEY_ENV',
                ],
                'client_id' => [
                    'label' => 'Client ID',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'AIRTEL_MONEY_CLIENT_ID',
                ],
                'client_secret' => [
                    'label' => 'Client Secret',
                    'type' => 'password',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'AIRTEL_MONEY_CLIENT_SECRET',
                    'conceal' => true,
                ],
                'country' => [
                    'label' => 'Primary Country ISO Code',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'size:2'],
                    'env' => 'AIRTEL_MONEY_COUNTRY',
                ],
                'currency' => [
                    'label' => 'Default Currency',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:10'],
                    'env' => 'AIRTEL_MONEY_CURRENCY',
                ],
                'callback_url' => [
                    'label' => 'Callback URL',
                    'type' => 'url',
                    'rules' => ['nullable', 'url', 'max:255'],
                    'env' => 'AIRTEL_MONEY_CALLBACK_URL',
                ],
            ],
        ],
        'pesapal' => [
            'label' => 'PesaPal',
            'description' => 'Collect payments via PesaPal.',
            'fields' => [
                'environment' => [
                    'label' => 'Environment',
                    'type' => 'select',
                    'options' => [
                        'sandbox' => 'Sandbox',
                        'live' => 'Live',
                    ],
                    'rules' => ['required_with:gateways.pesapal.is_enabled', 'in:sandbox,live'],
                    'default' => 'sandbox',
                    'env' => 'PESAPAL_ENV',
                ],
                'consumer_key' => [
                    'label' => 'Consumer Key',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'PESAPAL_CONSUMER_KEY',
                ],
                'consumer_secret' => [
                    'label' => 'Consumer Secret',
                    'type' => 'password',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'PESAPAL_CONSUMER_SECRET',
                    'conceal' => true,
                ],
                'callback_url' => [
                    'label' => 'Callback URL',
                    'type' => 'url',
                    'rules' => ['nullable', 'url', 'max:255'],
                    'env' => 'PESAPAL_CALLBACK_URL',
                ],
                'account_reference' => [
                    'label' => 'Account Reference',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'env' => 'PESAPAL_ACCOUNT_REFERENCE',
                ],
            ],
        ],
        'bank_transfer' => [
            'label' => 'Bank Transfer / Direct Deposit',
            'description' => 'Display bank account details for manual payments. Students, parents, teachers, and staff will see these instructions on their payment pages.',
            'fields' => [
                'bank_name' => [
                    'label' => 'Bank Name',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:255'],
                ],
                'account_name' => [
                    'label' => 'Account Name / Beneficiary Name',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:255'],
                ],
                'account_number' => [
                    'label' => 'Account Number',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:100'],
                ],
                'branch_name' => [
                    'label' => 'Branch Name',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:255'],
                ],
                'branch_code' => [
                    'label' => 'Branch Code / Sort Code',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:50'],
                ],
                'swift_code' => [
                    'label' => 'SWIFT / BIC Code (for international transfers)',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:50'],
                ],
                'iban' => [
                    'label' => 'IBAN (International Bank Account Number)',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:50'],
                ],
                'routing_number' => [
                    'label' => 'Routing Number / ABA Number (USA)',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:50'],
                ],
                'payment_instructions' => [
                    'label' => 'Payment Instructions',
                    'type' => 'textarea',
                    'rules' => ['nullable', 'string', 'max:1000'],
                    'default' => 'Please include your student ID or invoice number as the payment reference.',
                ],
                'additional_info' => [
                    'label' => 'Additional Information',
                    'type' => 'textarea',
                    'rules' => ['nullable', 'string', 'max:500'],
                ],
            ],
        ],
        'custom' => [
            'label' => 'Custom Gateway',
            'description' => 'Define credentials for an additional payment provider.',
            'fields' => [
                'provider_name' => [
                    'label' => 'Provider Name',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:120'],
                ],
                'base_url' => [
                    'label' => 'API Base URL',
                    'type' => 'url',
                    'rules' => ['nullable', 'url', 'max:255'],
                ],
                'public_key' => [
                    'label' => 'Public Key',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:255'],
                ],
                'secret_key' => [
                    'label' => 'Secret Key',
                    'type' => 'password',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'conceal' => true,
                ],
                'webhook_secret' => [
                    'label' => 'Webhook Secret',
                    'type' => 'password',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'conceal' => true,
                ],
                'callback_url' => [
                    'label' => 'Callback URL',
                    'type' => 'url',
                    'rules' => ['nullable', 'url', 'max:255'],
                ],
                'metadata' => [
                    'label' => 'Additional Metadata (JSON)',
                    'type' => 'textarea',
                    'rules' => ['nullable', 'string'],
                ],
            ],
        ],
    ],
];
