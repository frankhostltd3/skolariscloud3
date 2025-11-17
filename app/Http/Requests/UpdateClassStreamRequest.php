<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassStreamRequest extends FormRequest
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
        $classId = $this->route('class')->id;
        $streamId = $this->route('stream')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($classId, $streamId) {
                    $exists = \App\Models\Academic\ClassStream::where('class_id', $classId)
                        ->where('name', $value)
                        ->where('id', '!=', $streamId)
                        ->exists();

                    if ($exists) {
                        $fail(__('A stream with this name already exists for this class.'));
                    }
                },
            ],
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'capacity' => 'nullable|integer|min:1|max:500',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => __('stream name'),
            'code' => __('stream code'),
            'description' => __('description'),
            'capacity' => __('capacity'),
            'is_active' => __('active status'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('The stream name is required.'),
            'name.max' => __('The stream name must not exceed 100 characters.'),
            'capacity.min' => __('The capacity must be at least 1.'),
            'capacity.max' => __('The capacity must not exceed 500.'),
        ];
    }
}
