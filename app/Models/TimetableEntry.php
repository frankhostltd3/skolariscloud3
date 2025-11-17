<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_of_week', // 1=Mon .. 7=Sun
        'starts_at',   // time
        'ends_at',     // time
        'class_id',
        'class_stream_id',
        'subject_id',
        'teacher_id',
        'room',
        'notes',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
    ];

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function stream()
    {
        return $this->belongsTo(ClassStream::class, 'class_stream_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
