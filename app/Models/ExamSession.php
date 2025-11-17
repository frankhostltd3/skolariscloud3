<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'class_id',
        'subject',
        'date',
        'start_time',
        'end_time',
        'room',
        'invigilator_id',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function class()
    {
        return $this->belongsTo(\App\Models\Academic\ClassRoom::class, 'class_id');
    }

    public function invigilator()
    {
        return $this->belongsTo(User::class, 'invigilator_id');
    }

    public function attendances()
    {
        return $this->hasMany(ExamAttendance::class);
    }

    public function paper()
    {
        return $this->hasOne(ExamPaper::class, 'exam_session_id');
    }

    public function submissions()
    {
        return $this->hasMany(ExamSubmission::class, 'exam_session_id');
    }
}