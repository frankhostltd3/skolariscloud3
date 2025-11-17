<?php

declare(strict_types=1);

namespace App\Http\Requests\Landlord;

use Illuminate\Foundation\Http\FormRequest;

class BillingPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'price_amount' => ['nullable', 'numeric', 'min:0'],
            'price_display' => ['nullable', 'string', 'max:60'],
            'currency' => ['required', 'string', 'size:3'],
            'billing_period' => ['required', 'string', 'max:40'],
            'billing_period_label' => ['required', 'string', 'max:60'],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'is_highlighted' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
            'features' => ['nullable', 'string'],
        ];
    }
}
