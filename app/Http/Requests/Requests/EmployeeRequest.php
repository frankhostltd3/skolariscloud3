<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
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
            'employee_type' => 'required|string',
            'national_id' => 'nullable|string|max:50|unique:employees,national_id',
            'gender' => ['nullable', Rule::in(['male','female','other'])],
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'salary_scale_id' => 'nullable|exists:salary_scales,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'hire_date' => 'nullable|date',
            'birth_date' => 'nullable|date',
            'employment_status' => 'nullable|string|in:active,probation,on_leave,inactive,terminated,suspended',
            // Allow larger dimensions; resizing service will downscale to 256x256 max
            'passport_photo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:133120',
        ];

        // For updates, exclude current record from unique validation
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $employeeId = $this->route('employee')?->id ?? $this->route('employee');
            $rules['email'] = 'nullable|email|unique:employees,email,' . $employeeId;
            $rules['national_id'] = 'nullable|string|max:50|unique:employees,national_id,' . $employeeId;
        }

        return $rules;
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already in use by another employee.',
            'employment_status.in' => 'Invalid employment status selected.',
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'department_id.required' => 'Please select a department.',
            'position_id.required' => 'Please select a position.',
            'gender.in' => 'Gender must be one of male, female, other.',
            'passport_photo.mimes' => 'Passport photo must be a JPG, PNG or WEBP file.',
            'passport_photo.max' => 'Passport photo must not exceed 130MB.',
            // dimensions rule removed in favor of automatic resizing
        ];
    }
}
