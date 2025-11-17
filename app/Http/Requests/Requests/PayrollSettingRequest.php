<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayrollSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [];

        // Get all settings that need validation
        $settings = \App\Models\PayrollSetting::where('is_active', true)->get();

        foreach ($settings as $setting) {
            $key = $setting->key;
            $fieldRules = $setting->validation_rules ?? [];

            if (!empty($fieldRules)) {
                $rules[$key] = $fieldRules;
            } else {
                // Default validation based on type
                switch ($setting->type) {
                    case 'number':
                        $rules[$key] = 'nullable|numeric';
                        break;
                    case 'boolean':
                        $rules[$key] = 'nullable|boolean';
                        break;
                    case 'date':
                        $rules[$key] = 'nullable|date';
                        break;
                    case 'select':
                        if ($setting->options) {
                            $rules[$key] = 'nullable|in:' . implode(',', array_keys($setting->options));
                        } else {
                            $rules[$key] = 'nullable|string';
                        }
                        break;
                    default:
                        $rules[$key] = 'nullable|string|max:255';
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [];
        $settings = \App\Models\PayrollSetting::where('is_active', true)->get();

        foreach ($settings as $setting) {
            $attributes[$setting->key] = $setting->label;
        }

        return $attributes;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert checkbox values to boolean
        $booleanFields = \App\Models\PayrollSetting::where('type', 'boolean')
                                                  ->where('is_active', true)
                                                  ->pluck('key')
                                                  ->toArray();

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([$field => $this->boolean($field)]);
            }
        }
    }
}
