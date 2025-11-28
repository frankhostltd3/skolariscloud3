<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $department = $this->route('department');
        $departmentId = $department ? $department->id : null;

        return [
            'name' => 'required|string|max:191|unique:tenant.departments,name,' . $departmentId,
            'code' => 'nullable|string|max:191|unique:tenant.departments,code,' . $departmentId,
            'description' => 'nullable|string',
        ];
    }
}
