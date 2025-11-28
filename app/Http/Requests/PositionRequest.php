<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PositionRequest extends FormRequest
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
        $position = $this->route('position');
        $positionId = $position ? $position->id : null;

        return [
            'title' => 'required|string|max:191|unique:tenant.positions,title,' . $positionId,
            'department_id' => 'required|exists:tenant.departments,id',
            'code' => 'nullable|string|max:191|unique:tenant.positions,code,' . $positionId,
            'description' => 'nullable|string',
        ];
    }
}
