<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VirtualClassAttendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'virtual_class_id',
        'student_id',
        'joined_at',
        'left_at',
        'duration_minutes',
        'status',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function virtualClass()
    {
        return $this->belongsTo(VirtualClass::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Accessors
     */
    public function getDurationFormattedAttribute()
    {
        if (!$this->duration_minutes) {
            return 'N/A';
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . ' minutes';
    }
}
