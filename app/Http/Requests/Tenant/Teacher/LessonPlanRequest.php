<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant\Teacher;

use App\Models\LessonPlan;
use Illuminate\Foundation\Http\FormRequest;

abstract class LessonPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $statusOptions = implode(',', array_keys(LessonPlan::statusOptions()));
        $reviewOptions = implode(',', array_keys(LessonPlan::reviewStatusOptions()));

        return [
            'title' => ['required', 'string', 'max:255'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'lesson_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'duration_minutes' => ['nullable', 'integer', 'min:5', 'max:600'],
            'objectives' => ['nullable', 'array'],
            'objectives.*' => ['nullable', 'string', 'max:500'],
            'materials_needed' => ['nullable', 'array'],
            'materials_needed.*' => ['nullable', 'string', 'max:255'],
            'introduction' => ['nullable', 'string'],
            'main_content' => ['nullable', 'string'],
            'activities' => ['nullable', 'array'],
            'activities.*' => ['nullable', 'string', 'max:500'],
            'assessment' => ['nullable', 'string'],
            'homework' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', 'in:'.$statusOptions],
            'review_status' => ['nullable', 'in:'.$reviewOptions],
            'is_template' => ['sometimes', 'boolean'],
            'requires_revision' => ['sometimes', 'boolean'],
            'action' => ['nullable', 'string', 'in:save_draft,submit,submit_for_review,resubmit'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'class_id' => $this->filled('class_id') ? (int) $this->input('class_id') : null,
            'subject_id' => $this->filled('subject_id') ? (int) $this->input('subject_id') : null,
            'lesson_date' => $this->input('lesson_date') ?: null,
            'duration_minutes' => $this->filled('duration_minutes') ? (int) $this->input('duration_minutes') : null,
            'objectives' => $this->normalizeList($this->input('objectives', [])),
            'materials_needed' => $this->normalizeList($this->input('materials_needed', [])),
            'activities' => $this->normalizeList($this->input('activities', [])),
            'assessment' => $this->nullableString('assessment'),
            'homework' => $this->nullableString('homework'),
            'notes' => $this->nullableString('notes'),
            'introduction' => $this->nullableString('introduction'),
            'main_content' => $this->nullableString('main_content'),
            'status' => $this->input('status') ?: LessonPlan::STATUS_DRAFT,
            'review_status' => $this->input('review_status') ?: LessonPlan::REVIEW_NOT_SUBMITTED,
            'is_template' => $this->boolean('is_template'),
        ]);
    }

    protected function normalizeList($value): ?array
    {
        if (! is_array($value)) {
            return null;
        }

        $items = array_values(array_filter(array_map(function ($item) {
            if (is_string($item)) {
                $trimmed = trim($item);

                return $trimmed === '' ? null : $trimmed;
            }

            return $item;
        }, $value), static fn ($item) => ! is_null($item)));

        return $items === [] ? null : $items;
    }

    protected function nullableString(string $key): ?string
    {
        $value = $this->input($key);

        if (is_string($value)) {
            $trimmed = trim($value);

            return $trimmed === '' ? null : $trimmed;
        }

        return $value ?: null;
    }
}
