<?php

namespace App\Services;

use App\Models\PaymentGatewaySetting;
use Illuminate\Support\Arr;

class PaymentGatewayConfigurator
{
    private array $baseServices;

    public function __construct()
    {
        $this->baseServices = config('services', []);
    }

    public function apply(): void
    {
        config(['services' => $this->baseServices]);

        try {
            $settings = PaymentGatewaySetting::query()->where('is_enabled', true)->get();
        } catch (\Throwable $exception) {
            return;
        }

        foreach ($settings as $setting) {
            $this->applyGateway($setting->gateway, $setting->config ?? []);
        }
    }

    private function applyGateway(string $gateway, array $config): void
    {
        switch ($gateway) {
            case 'paypal':
                config([
                    'services.paypal.mode' => $config['mode'] ?? 'sandbox',
                    'services.paypal.client_id' => $config['client_id'] ?? null,
                    'services.paypal.client_secret' => $config['client_secret'] ?? null,
                    'services.paypal.webhook_id' => $config['webhook_id'] ?? null,
                ]);
                break;
            case 'stripe':
                config([
                    'services.stripe.mode' => $config['mode'] ?? 'test',
                    'services.stripe.publishable_key' => $config['publishable_key'] ?? null,
                    'services.stripe.secret' => $config['secret_key'] ?? null,
                    'services.stripe.webhook_secret' => $config['webhook_secret'] ?? null,
                ]);
                break;
            case 'flutterwave':
                config([
                    'services.flutterwave.environment' => $config['environment'] ?? 'sandbox',
                    'services.flutterwave.public_key' => $config['public_key'] ?? null,
                    'services.flutterwave.secret_key' => $config['secret_key'] ?? null,
                    'services.flutterwave.encryption_key' => $config['encryption_key'] ?? null,
                    'services.flutterwave.webhook_secret' => $config['webhook_secret'] ?? null,
                ]);
                break;
            case 'mtn_momo':
                config([
                    'services.mtn_momo.environment' => $config['environment'] ?? 'sandbox',
                    'services.mtn_momo.primary_key' => $config['primary_key'] ?? null,
                    'services.mtn_momo.secondary_key' => $config['secondary_key'] ?? null,
                    'services.mtn_momo.subscription_key' => $config['subscription_key'] ?? null,
                    'services.mtn_momo.callback_url' => $config['callback_url'] ?? null,
                    'services.mtn_momo.country_codes' => Arr::where(array_map('trim', explode(',', (string) ($config['country_codes'] ?? ''))), static fn ($value) => $value !== ''),
                ]);
                break;
            case 'airtel_money':
                config([
                    'services.airtel_money.environment' => $config['environment'] ?? 'sandbox',
                    'services.airtel_money.client_id' => $config['client_id'] ?? null,
                    'services.airtel_money.client_secret' => $config['client_secret'] ?? null,
                    'services.airtel_money.country' => $config['country'] ?? null,
                    'services.airtel_money.currency' => $config['currency'] ?? null,
                    'services.airtel_money.callback_url' => $config['callback_url'] ?? null,
                ]);
                break;
            case 'pesapal':
                config([
                    'services.pesapal.environment' => $config['environment'] ?? 'sandbox',
                    'services.pesapal.consumer_key' => $config['consumer_key'] ?? null,
                    'services.pesapal.consumer_secret' => $config['consumer_secret'] ?? null,
                    'services.pesapal.callback_url' => $config['callback_url'] ?? null,
                    'services.pesapal.account_reference' => $config['account_reference'] ?? null,
                ]);
                break;
            case 'custom':
                config([
                    'services.custom_gateway.provider_name' => $config['provider_name'] ?? null,
                    'services.custom_gateway.base_url' => $config['base_url'] ?? null,
                    'services.custom_gateway.public_key' => $config['public_key'] ?? null,
                    'services.custom_gateway.secret_key' => $config['secret_key'] ?? null,
                    'services.custom_gateway.webhook_secret' => $config['webhook_secret'] ?? null,
                    'services.custom_gateway.callback_url' => $config['callback_url'] ?? null,
                    'services.custom_gateway.metadata' => $this->decodeMetadata($config['metadata'] ?? null),
                ]);
                break;
            default:
                break;
        }
    }

    private function decodeMetadata(?string $raw): array
    {
        if (! $raw) {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }
}
