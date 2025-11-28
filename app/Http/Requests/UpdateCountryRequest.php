<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCountryRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'iso_code_2' => 'required|string|size:2|unique:countries,iso_code_2,' . $this->country->id,
            'iso_code_3' => 'required|string|size:3|unique:countries,iso_code_3,' . $this->country->id,
            'phone_code' => 'nullable|string|max:20',
            'currency_code' => 'nullable|string|max:3',
            'currency_symbol' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:100',
            'flag_emoji' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ];
    }
}
