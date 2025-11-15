<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'category_id',
        'expense_name',
        'amount',
        'expense_date',
        'payment_method',
        'reference_number',
        'vendor',
        'description',
        'created_by',
        'approved_by',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    /**
     * Get the school that owns the expense.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the category that owns the expense.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    /**
     * Get the user who created the expense.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the expense.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include expenses for a specific school.
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include approved expenses.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending expenses.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'approved' => 'bg-success',
            'pending' => 'bg-warning',
            'rejected' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
