<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradingSchemeRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'examination_body_id' => 'nullable|exists:examination_bodies,id',
            'description' => 'nullable|string|max:1000',
            'is_current' => 'boolean',
            'is_active' => 'boolean',

            // Grading bands validation
            'bands' => 'nullable|array',
            'bands.*.grade' => 'required_with:bands|string|max:10',
            'bands.*.label' => 'nullable|string|max:255',
            'bands.*.min_score' => 'required_with:bands|numeric|min:0|max:100',
            'bands.*.max_score' => 'required_with:bands|numeric|min:0|max:100|gte:bands.*.min_score',
            'bands.*.grade_point' => 'nullable|numeric|min:0|max:10',
            'bands.*.remarks' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Grading scheme name is required.',
            'bands.*.grade.required_with' => 'Grade is required for each band.',
            'bands.*.min_score.required_with' => 'Minimum score is required for each band.',
            'bands.*.max_score.required_with' => 'Maximum score is required for each band.',
            'bands.*.max_score.gte' => 'Maximum score must be greater than or equal to minimum score.',
        ];
    }
}
