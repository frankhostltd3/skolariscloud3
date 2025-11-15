<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMessagingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $channels = config('messaging.channels', []);
        $rules = [
            'channels' => ['array'],
            'sync_env' => ['array'],
        ];

        foreach ($channels as $channelKey => $channelDefinition) {
            $providers = $channelDefinition['providers'] ?? [];

            foreach ($providers as $providerKey => $providerDefinition) {
                $rules["channels.$channelKey.providers.$providerKey.is_enabled"] = ['nullable', 'boolean'];

                $fields = $providerDefinition['fields'] ?? [];

                foreach ($fields as $fieldKey => $fieldDefinition) {
                    $fieldRules = $fieldDefinition['rules'] ?? ['nullable', 'string'];
                    $rules["channels.$channelKey.providers.$providerKey.config.$fieldKey"] = $fieldRules;
                }
            }
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'channels' => $this->input('channels', []),
            'sync_env' => $this->input('sync_env', []),
        ]);
    }
}
