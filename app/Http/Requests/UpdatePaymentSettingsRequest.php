<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $gateways = config('payment_gateways.gateways', []);
        $rules = [
            'gateways' => ['array'],
            'sync_env' => ['array'],
        ];

        foreach ($gateways as $key => $definition) {
            $rules["gateways.$key.is_enabled"] = ['nullable', 'boolean'];

            $fields = $definition['fields'] ?? [];

            foreach ($fields as $fieldKey => $fieldDefinition) {
                $fieldRules = $fieldDefinition['rules'] ?? ['nullable', 'string'];
                $rules["gateways.$key.config.$fieldKey"] = $fieldRules;
            }
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'gateways' => $this->input('gateways', []),
            'sync_env' => $this->input('sync_env', []),
        ]);
    }
}
