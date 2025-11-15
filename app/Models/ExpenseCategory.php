<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the school that owns the category.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the expenses for the category.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category_id');
    }

    /**
     * Get the transactions for the category.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }

    /**
     * Scope a query to only include categories for a specific school.
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
