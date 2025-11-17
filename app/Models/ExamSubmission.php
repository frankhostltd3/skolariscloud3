<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_session_id',
        'student_id',
        'answers',
        'marks_obtained',
        'total_marks',
        'feedback',
        'submitted_at',
        'marked_at',
        'marked_by',
    ];

    protected $casts = [
        'answers' => 'array',
        'submitted_at' => 'datetime',
        'marked_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function marker()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}