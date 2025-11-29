<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentBehaviour extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'reporter_id',
        'type',
        'category',
        'title',
        'description',
        'points',
        'incident_date',
        'action_taken',
        'status',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'points' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function scopePositive($query)
    {
        return $query->where('type', 'positive');
    }

    public function scopeNegative($query)
    {
        return $query->where('type', 'negative');
    }
}
