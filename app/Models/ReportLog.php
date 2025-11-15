<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportLog extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'user_id',
        'name',
        'type',
        'status',
        'rows_count',
        'size_bytes',
        'file_path',
        'error',
        'generated_at',
    ];

    protected $casts = [
        'rows_count' => 'integer',
        'size_bytes' => 'integer',
        'generated_at' => 'datetime',
    ];

    /**
     * Get the school that owns the report.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the user who generated the report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include reports for a specific school.
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope a query to only include completed reports.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include failed reports.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
