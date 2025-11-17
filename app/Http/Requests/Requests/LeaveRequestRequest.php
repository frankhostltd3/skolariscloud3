<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaveRequestRequest extends FormRequest
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
        $rules = [
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days_requested' => 'sometimes|integer|min:1|max:365',
            'reason' => 'required|string|max:1000',
            'status' => 'sometimes|in:pending,approved,rejected',
        ];

        // When updating (approving/rejecting), manager_comment is required
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['manager_comment'] = 'required|string|max:1000';
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'employee_id' => 'employee',
            'leave_type_id' => 'leave type',
            'start_date' => 'start date',
            'end_date' => 'end date',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default status to pending if not provided
        if (!$this->has('status')) {
            $this->merge(['status' => 'pending']);
        }
    }
}
