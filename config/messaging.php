<?php

return [
    'channels' => [
        'sms' => [
            'label' => 'SMS Messaging',
            'description' => 'Send school-wide alerts, parent notifications, and landlord announcements through SMS gateways.',
            'providers' => [
                'twilio' => [
                    'label' => 'Twilio SMS',
                    'description' => 'Global SMS delivery with scalable throughput.',
                    'fields' => [
                        'account_sid' => [
                            'label' => 'Account SID',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:191'],
                            'env' => 'TWILIO_SMS_ACCOUNT_SID',
                        ],
                        'auth_token' => [
                            'label' => 'Auth Token',
                            'type' => 'password',
                            'rules' => ['nullable', 'string', 'max:191'],
                            'env' => 'TWILIO_SMS_AUTH_TOKEN',
                            'conceal' => true,
                        ],
                        'from_number' => [
                            'label' => 'Default From Number',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:30'],
                            'env' => 'TWILIO_SMS_FROM',
                        ],
                    ],
                    'config_map' => [
                        'services.twilio.sms.account_sid' => 'account_sid',
                        'services.twilio.sms.auth_token' => 'auth_token',
                        'services.twilio.sms.from' => 'from_number',
                    ],
                ],
                'vonage' => [
                    'label' => 'Vonage (Nexmo)',
                    'description' => 'SMS reach using Vonage Communications APIs.',
                    'fields' => [
                        'api_key' => [
                            'label' => 'API Key',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:191'],
                            'env' => 'VONAGE_SMS_API_KEY',
                        ],
                        'api_secret' => [
                            'label' => 'API Secret',
                            'type' => 'password',
                            'rules' => ['nullable', 'string', 'max:191'],
                            'env' => 'VONAGE_SMS_API_SECRET',
                            'conceal' => true,
                        ],
                        'from_number' => [
                            'label' => 'Sender ID / From Number',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:30'],
                            'env' => 'VONAGE_SMS_FROM',
                        ],
                    ],
                    'config_map' => [
                        'services.vonage.sms.api_key' => 'api_key',
                        'services.vonage.sms.api_secret' => 'api_secret',
                        'services.vonage.sms.from' => 'from_number',
                    ],
                ],
                'africastalking' => [
                    'label' => "Africa's Talking",
                    'description' => 'Regional SMS connectivity across African carriers.',
                    'fields' => [
                        'username' => [
                            'label' => 'Username',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:191'],
                            'env' => 'AFRICASTALKING_SMS_USERNAME',
                        ],
                        'api_key' => [
                            'label' => 'API Key',
                            'type' => 'password',
                            'rules' => ['nullable', 'string', 'max:191'],
                            'env' => 'AFRICASTALKING_SMS_API_KEY',
                            'conceal' => true,
                        ],
                        'shortcode' => [
                            'label' => 'Sender ID / Shortcode',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:30'],
                            'env' => 'AFRICASTALKING_SMS_FROM',
                        ],
                    ],
                    'config_map' => [
                        'services.africastalking.sms.username' => 'username',
                        'services.africastalking.sms.api_key' => 'api_key',
                        'services.africastalking.sms.from' => 'shortcode',
                    ],
                ],
                'custom' => [
                    'label' => 'Custom SMS API',
                    'description' => 'Bring your own SMS provider with REST integration.',
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
                        'access_key' => [
                            'label' => 'Access Key',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:255'],
                            'env' => 'CUSTOM_SMS_ACCESS_KEY',
                        ],
                        'secret_key' => [
                            'label' => 'Secret Key',
                            'type' => 'password',
                            'rules' => ['nullable', 'string', 'max:255'],
                            'env' => 'CUSTOM_SMS_SECRET_KEY',
                            'conceal' => true,
                        ],
                        'from' => [
                            'label' => 'Sender ID',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:30'],
                            'env' => 'CUSTOM_SMS_FROM',
                        ],
                        'metadata' => [
                            'label' => 'Additional Metadata (JSON)',
                            'type' => 'textarea',
                            'rules' => ['nullable', 'string'],
                            'cast' => 'json',
                        ],
                    ],
                    'config_map' => [
                        'services.custom_sms.provider_name' => 'provider_name',
                        'services.custom_sms.base_url' => 'base_url',
                        'services.custom_sms.access_key' => 'access_key',
                        'services.custom_sms.secret_key' => 'secret_key',
                        'services.custom_sms.from' => 'from',
                        'services.custom_sms.metadata' => 'metadata',
                    ],
                ],
            ],
        ],
        'whatsapp' => [
            'label' => 'WhatsApp Messaging',
            'description' => 'Reach guardians and community groups using WhatsApp Business APIs.',
            'providers' => [
                'twilio_whatsapp' => [
                    'label' => 'Twilio WhatsApp',
                    'description' => 'Send WhatsApp templates and session messages via Twilio.',
                    'fields' => [
                        'account_sid' => [
                            'label' => 'Account SID',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:191'],
                            'env' => 'TWILIO_WHATSAPP_ACCOUNT_SID',
                        ],
                        'auth_token' => [
                            'label' => 'Auth Token',
                            'type' => 'password',
                            'rules' => ['nullable', 'string', 'max:191'],
                            'env' => 'TWILIO_WHATSAPP_AUTH_TOKEN',
                            'conceal' => true,
                        ],
                        'from_number' => [
                            'label' => 'WhatsApp Sender (e.g., whatsapp:+1234567890)',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:40'],
                            'env' => 'TWILIO_WHATSAPP_FROM',
                        ],
                    ],
                    'config_map' => [
                        'services.twilio.whatsapp.account_sid' => 'account_sid',
                        'services.twilio.whatsapp.auth_token' => 'auth_token',
                        'services.twilio.whatsapp.from' => 'from_number',
                    ],
                ],
                'meta_cloud' => [
                    'label' => 'Meta WhatsApp Cloud API',
                    'description' => 'Official WhatsApp Business Platform hosted by Meta.',
                    'fields' => [
                        'access_token' => [
                            'label' => 'Permanent Access Token',
                            'type' => 'password',
                            'rules' => ['nullable', 'string'],
                            'env' => 'META_WHATSAPP_ACCESS_TOKEN',
                            'conceal' => true,
                        ],
                        'phone_number_id' => [
                            'label' => 'Phone Number ID',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:120'],
                            'env' => 'META_WHATSAPP_PHONE_NUMBER_ID',
                        ],
                        'business_account_id' => [
                            'label' => 'Business Account ID',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:120'],
                            'env' => 'META_WHATSAPP_BUSINESS_ID',
                        ],
                        'verify_token' => [
                            'label' => 'Webhook Verify Token',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:120'],
                            'env' => 'META_WHATSAPP_VERIFY_TOKEN',
                        ],
                        'webhook_url' => [
                            'label' => 'Webhook URL',
                            'type' => 'url',
                            'rules' => ['nullable', 'url', 'max:255'],
                            'env' => 'META_WHATSAPP_WEBHOOK_URL',
                        ],
                    ],
                    'config_map' => [
                        'services.meta_whatsapp.access_token' => 'access_token',
                        'services.meta_whatsapp.phone_number_id' => 'phone_number_id',
                        'services.meta_whatsapp.business_account_id' => 'business_account_id',
                        'services.meta_whatsapp.verify_token' => 'verify_token',
                        'services.meta_whatsapp.webhook_url' => 'webhook_url',
                    ],
                ],
                'custom' => [
                    'label' => 'Custom WhatsApp Provider',
                    'description' => 'Integrate alternative WhatsApp aggregators or gateways.',
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
                        'api_key' => [
                            'label' => 'API Key',
                            'type' => 'password',
                            'rules' => ['nullable', 'string', 'max:255'],
                            'env' => 'CUSTOM_WHATSAPP_API_KEY',
                            'conceal' => true,
                        ],
                        'sender' => [
                            'label' => 'Sender / Phone Number',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:40'],
                            'env' => 'CUSTOM_WHATSAPP_SENDER',
                        ],
                        'metadata' => [
                            'label' => 'Additional Metadata (JSON)',
                            'type' => 'textarea',
                            'rules' => ['nullable', 'string'],
                            'cast' => 'json',
                        ],
                    ],
                    'config_map' => [
                        'services.custom_whatsapp.provider_name' => 'provider_name',
                        'services.custom_whatsapp.base_url' => 'base_url',
                        'services.custom_whatsapp.api_key' => 'api_key',
                        'services.custom_whatsapp.sender' => 'sender',
                        'services.custom_whatsapp.metadata' => 'metadata',
                    ],
                ],
            ],
        ],
        'telegram' => [
            'label' => 'Telegram Messaging',
            'description' => 'Send automated updates, alerts, and notifications through Telegram bots.',
            'providers' => [
                'telegram_bot' => [
                    'label' => 'Telegram Bot API',
                    'description' => 'Official Telegram Bot API for broadcasting messages to channels and groups.',
                    'fields' => [
                        'bot_token' => [
                            'label' => 'Bot Token',
                            'type' => 'password',
                            'rules' => ['nullable', 'string', 'max:255'],
                            'env' => 'TELEGRAM_BOT_TOKEN',
                            'conceal' => true,
                        ],
                        'bot_username' => [
                            'label' => 'Bot Username',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:120'],
                            'env' => 'TELEGRAM_BOT_USERNAME',
                        ],
                        'default_chat_id' => [
                            'label' => 'Default Chat/Channel ID',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:120'],
                            'env' => 'TELEGRAM_DEFAULT_CHAT_ID',
                        ],
                        'webhook_url' => [
                            'label' => 'Webhook URL (Optional)',
                            'type' => 'url',
                            'rules' => ['nullable', 'url', 'max:255'],
                            'env' => 'TELEGRAM_WEBHOOK_URL',
                        ],
                        'parse_mode' => [
                            'label' => 'Parse Mode',
                            'type' => 'select',
                            'options' => [
                                '' => 'None',
                                'Markdown' => 'Markdown',
                                'MarkdownV2' => 'MarkdownV2',
                                'HTML' => 'HTML',
                            ],
                            'default' => 'HTML',
                            'rules' => ['nullable', 'string', 'in:,Markdown,MarkdownV2,HTML'],
                            'env' => 'TELEGRAM_PARSE_MODE',
                        ],
                    ],
                    'config_map' => [
                        'services.telegram.bot_token' => 'bot_token',
                        'services.telegram.bot_username' => 'bot_username',
                        'services.telegram.default_chat_id' => 'default_chat_id',
                        'services.telegram.webhook_url' => 'webhook_url',
                        'services.telegram.parse_mode' => 'parse_mode',
                    ],
                ],
                'custom' => [
                    'label' => 'Custom Telegram Provider',
                    'description' => 'Integrate third-party Telegram gateway or aggregator.',
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
                        'api_key' => [
                            'label' => 'API Key',
                            'type' => 'password',
                            'rules' => ['nullable', 'string', 'max:255'],
                            'env' => 'CUSTOM_TELEGRAM_API_KEY',
                            'conceal' => true,
                        ],
                        'bot_id' => [
                            'label' => 'Bot ID / Identifier',
                            'type' => 'text',
                            'rules' => ['nullable', 'string', 'max:120'],
                            'env' => 'CUSTOM_TELEGRAM_BOT_ID',
                        ],
                        'metadata' => [
                            'label' => 'Additional Metadata (JSON)',
                            'type' => 'textarea',
                            'rules' => ['nullable', 'string'],
                            'cast' => 'json',
                        ],
                    ],
                    'config_map' => [
                        'services.custom_telegram.provider_name' => 'provider_name',
                        'services.custom_telegram.base_url' => 'base_url',
                        'services.custom_telegram.api_key' => 'api_key',
                        'services.custom_telegram.bot_id' => 'bot_id',
                        'services.custom_telegram.metadata' => 'metadata',
                    ],
                ],
            ],
        ],
    ],
];
