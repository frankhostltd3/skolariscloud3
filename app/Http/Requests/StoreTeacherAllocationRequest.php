<?php

namespace App\Http\Requests;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreTeacherAllocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->user_type === UserType::ADMIN;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'teacher_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('school_id', auth()->user()->school_id)
                          ->where('user_type', UserType::TEACHING_STAFF->value)
                          ->where('is_active', true);
                })
            ],
            'class_id' => [
                'required',
                'integer',
                Rule::exists('classes', 'id')->where(function ($query) {
                    $query->where('school_id', auth()->user()->school_id);
                })
            ],
            'subject_id' => [
                'required',
                'integer',
                Rule::exists('subjects', 'id')->where(function ($query) {
                    $query->where('school_id', auth()->user()->school_id);
                })
            ],
            'is_compulsory' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'teacher_id.required' => 'Please select a teacher.',
            'teacher_id.exists' => 'The selected teacher is invalid or not active.',
            'class_id.required' => 'Please select a class.',
            'class_id.exists' => 'The selected class does not exist.',
            'subject_id.required' => 'Please select a subject.',
            'subject_id.exists' => 'The selected subject does not exist.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        // Validation logic removed to allow creating new class-subject assignments on the fly
    }
}
