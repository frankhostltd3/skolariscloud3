<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TuitionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'academic_year',
        'grade_level',
        'total_amount',
        'currency_id',
        'installment_count',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created this tuition plan.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the currency for this tuition plan.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the tuition plan items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(TuitionPlanItem::class);
    }

    /**
     * Get the tuition plan installments.
     */
    public function installments(): HasMany
    {
        return $this->hasMany(TuitionPlanInstallment::class)->orderBy('installment_number');
    }

    /**
     * Get the formatted total amount.
     */
    protected function formattedTotalAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->currency ? $this->currency->formatAmount($this->total_amount) : number_format($this->total_amount, 2)
        );
    }

    /**
     * Get the formatted currency amount.
     */
    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->currency ? $this->currency->formatAmount($this->total_amount) : '$' . number_format($this->total_amount, 2)
        );
    }

    /**
     * Scope to get active tuition plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by academic year.
     */
    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Scope to filter by grade level.
     */
    public function scopeByGradeLevel($query, $gradeLevel)
    {
        return $query->where('grade_level', $gradeLevel);
    }
}