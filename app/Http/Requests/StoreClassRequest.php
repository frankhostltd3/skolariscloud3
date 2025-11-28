<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:tenant.classes,code,NULL,id,school_id,' . $school->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'education_level_id' => ['nullable', 'exists:tenant.education_levels,id'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:500'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'class name',
            'code' => 'class code',
            'education_level_id' => 'education level',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a class name.',
            'code.unique' => 'This class code is already in use at your school.',
            'capacity.min' => 'Class capacity must be at least 1 student.',
            'capacity.max' => 'Class capacity cannot exceed 500 students.',
        ];
    }
}
