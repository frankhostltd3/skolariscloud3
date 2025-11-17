<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubjectRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $school = $this->attributes->get('currentSchool') ?? auth()->user()->school;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('subjects')->where(function ($query) use ($school) {
                    return $query->where('school_id', $school->id);
                }),
            ],
            'education_level_id' => 'nullable|exists:education_levels,id',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:core,elective,optional',
            'credit_hours' => 'nullable|integer|min:0|max:100',
            'pass_mark' => 'nullable|integer|min:0|max:100',
            'max_marks' => 'nullable|integer|min:1|max:1000|gte:pass_mark',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Subject name is required.',
            'code.unique' => 'This subject code is already in use.',
            'type.required' => 'Subject type is required.',
            'type.in' => 'Subject type must be core, elective, or optional.',
            'max_marks.gte' => 'Maximum marks must be greater than or equal to pass mark.',
        ];
    }
}
