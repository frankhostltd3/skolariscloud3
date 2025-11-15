<?php

namespace App\Services;

use App\Models\MessagingChannelSetting;

class MessagingConfigurator
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
            $settings = MessagingChannelSetting::query()->where('is_enabled', true)->get();
        } catch (\Throwable $exception) {
            return;
        }

        $definitions = config('messaging.channels', []);

        foreach ($settings as $setting) {
            $channelDefinitions = $definitions[$setting->channel] ?? null;

            if (! $channelDefinitions) {
                continue;
            }

            $providers = $channelDefinitions['providers'] ?? [];
            $definition = $providers[$setting->provider] ?? null;

            if (! $definition) {
                continue;
            }

            $fields = $definition['fields'] ?? [];

            $this->applyProviderConfig($setting->config ?? [], $definition['config_map'] ?? [], $fields);
        }
    }

    private function applyProviderConfig(array $config, array $map, array $fields): void
    {
        foreach ($map as $configKey => $fieldKey) {
            $value = $config[$fieldKey] ?? null;

            if ($value !== null && ($fields[$fieldKey]['cast'] ?? null) === 'json') {
                $decoded = json_decode((string) $value, true);
                $value = is_array($decoded) ? $decoded : $value;
            }

            config([$configKey => $value]);
        }
    }
}
