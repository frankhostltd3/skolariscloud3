<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaveTypeRequest extends FormRequest
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
            'code' => 'required|string|max:10|unique:leave_types,code,' . ($this->route('leave_type') ? $this->route('leave_type')->id : ''),
            'default_days' => 'required|integer|min:0|max:365',
            'requires_approval' => 'boolean',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
