<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'title',
        'objectives',
        'materials_needed',
        'introduction',
        'main_content',
        'activities',
        'assessment',
        'homework',
        'notes',
        'status',
        'is_template',
        'lesson_date',
    ];

    protected $casts = [
        'objectives' => 'array',
        'materials_needed' => 'array',
        'activities' => 'array',
        'is_template' => 'boolean',
        'lesson_date' => 'date',
    ];

    protected $appends = ['status_label'];

    /**
     * Relationships
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Scopes
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Accessors
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'published' => 'Published',
            'completed' => 'Completed',
            default => 'Unknown'
        };
    }

    /**
     * Methods
     */
    public function publish()
    {
        $this->update(['status' => 'published']);
    }

    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    public function saveAsTemplate()
    {
        $template = $this->replicate();
        $template->is_template = true;
        $template->status = 'draft';
        $template->lesson_date = null;
        $template->save();

        return $template;
    }

    public function createFromTemplate($classId, $lessonDate)
    {
        $plan = $this->replicate();
        $plan->is_template = false;
        $plan->class_id = $classId;
        $plan->lesson_date = $lessonDate;
        $plan->status = 'draft';
        $plan->save();

        return $plan;
    }
}
