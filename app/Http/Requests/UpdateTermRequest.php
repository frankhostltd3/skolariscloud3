<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTermRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->user_type === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $schoolId = auth()->user()->school_id;
        $termId = $this->route('term')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('terms')
                    ->where('school_id', $schoolId)
                    ->where('academic_year', $this->input('academic_year'))
                    ->ignore($termId)
            ],
            'code' => 'nullable|string|max:20',
            'academic_year' => [
                'required',
                'string',
                'max:20',
                'regex:/^\d{4}(\/\d{4})?$/' // Matches "2025" or "2024/2025"
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string|max:500',
            'is_current' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'A term with this name already exists for the selected academic year.',
            'academic_year.regex' => 'Academic year must be in format YYYY or YYYY/YYYY (e.g., 2025 or 2024/2025).',
            'end_date.after' => 'End date must be after the start date.',
        ];
    }
}
