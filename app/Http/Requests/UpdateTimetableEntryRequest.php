<?php

namespace App\Http\Requests;

use App\Models\Academic\TimetableEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTimetableEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admins and teachers can update timetable entries
        return auth()->user()->hasAnyRole(['admin', 'teacher']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $schoolId = auth()->user()->school_id;

        return [
            'class_id' => [
                'required',
                'integer',
                Rule::exists('classes', 'id')->where('school_id', $schoolId),
            ],
            'class_stream_id' => [
                'nullable',
                'integer',
                Rule::exists('class_streams', 'id')->where(function ($query) {
                    $query->where('class_id', $this->input('class_id'));
                }),
            ],
            'subject_id' => [
                'required',
                'integer',
                Rule::exists('subjects', 'id')->where('school_id', $schoolId),
            ],
            'teacher_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')
                    ->where('school_id', $schoolId)
                    ->where('type', 'teacher')
                    ->where('is_active', true),
            ],
            'day_of_week' => [
                'required',
                'integer',
                'min:1',
                'max:7',
            ],
            'starts_at' => [
                'required',
                'date_format:H:i',
            ],
            'ends_at' => [
                'required',
                'date_format:H:i',
                'after:starts_at',
            ],
            'room' => [
                'nullable',
                'string',
                'max:50',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'class_id.required' => 'Please select a class.',
            'class_id.exists' => 'The selected class does not exist or does not belong to your school.',
            'class_stream_id.exists' => 'The selected stream does not belong to the selected class.',
            'subject_id.required' => 'Please select a subject.',
            'subject_id.exists' => 'The selected subject does not exist or does not belong to your school.',
            'teacher_id.exists' => 'The selected teacher does not exist, is not active, or does not belong to your school.',
            'day_of_week.required' => 'Please select a day of the week.',
            'day_of_week.min' => 'Day of week must be between 1 (Monday) and 7 (Sunday).',
            'day_of_week.max' => 'Day of week must be between 1 (Monday) and 7 (Sunday).',
            'starts_at.required' => 'Please enter a start time.',
            'starts_at.date_format' => 'Start time must be in HH:MM format (e.g., 08:00).',
            'ends_at.required' => 'Please enter an end time.',
            'ends_at.date_format' => 'End time must be in HH:MM format (e.g., 09:00).',
            'ends_at.after' => 'End time must be after start time.',
            'room.max' => 'Room name cannot exceed 50 characters.',
            'notes.max' => 'Notes cannot exceed 500 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $entryId = $this->route('timetable'); // Get the ID from route parameter

            // Check for teacher conflicts (excluding current entry)
            if ($this->filled('teacher_id') && !$validator->errors()->has('teacher_id')) {
                $conflict = TimetableEntry::forSchool(auth()->user()->school_id)
                    ->forTeacher($this->input('teacher_id'))
                    ->conflictsWith(
                        $this->input('day_of_week'),
                        $this->input('starts_at'),
                        $this->input('ends_at'),
                        $entryId
                    )
                    ->first();

                if ($conflict) {
                    $validator->errors()->add(
                        'teacher_id',
                        sprintf(
                            'This teacher already has a class scheduled at this time (%s %s - %s).',
                            $conflict->day_name,
                            $conflict->starts_at,
                            $conflict->ends_at
                        )
                    );
                }
            }

            // Check for class conflicts (excluding current entry)
            if ($this->filled('class_id') && !$validator->errors()->has('class_id')) {
                $query = TimetableEntry::forSchool(auth()->user()->school_id)
                    ->forClass($this->input('class_id'))
                    ->conflictsWith(
                        $this->input('day_of_week'),
                        $this->input('starts_at'),
                        $this->input('ends_at'),
                        $entryId
                    );

                // If stream is specified, check for that specific stream or general class entries
                if ($this->filled('class_stream_id')) {
                    $query->where(function ($q) {
                        $q->where('class_stream_id', $this->input('class_stream_id'))
                            ->orWhereNull('class_stream_id');
                    });
                }

                $conflict = $query->first();

                if ($conflict) {
                    $validator->errors()->add(
                        'class_id',
                        sprintf(
                            'This class already has a subject scheduled at this time (%s %s - %s: %s).',
                            $conflict->day_name,
                            $conflict->starts_at,
                            $conflict->ends_at,
                            $conflict->subject->name ?? 'Unknown'
                        )
                    );
                }
            }
        });
    }
}
