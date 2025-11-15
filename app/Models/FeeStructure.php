<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeStructure extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'fee_name',
        'fee_type',
        'amount',
        'academic_year',
        'term',
        'class',
        'due_date',
        'is_mandatory',
        'is_active',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the school that owns the fee structure.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the invoices for the fee structure.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Scope a query to only include fee structures for a specific school.
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope a query to only include active fee structures.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by academic year.
     */
    public function scopeAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Scope a query to filter by class.
     */
    public function scopeForClass($query, $class)
    {
        return $query->where(function($q) use ($class) {
            $q->where('class', $class)->orWhereNull('class');
        });
    }
}
