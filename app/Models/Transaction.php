<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'transaction_type',
        'amount',
        'description',
        'category_id',
        'payment_method',
        'reference_number',
        'created_by',
        'transaction_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the school that owns the transaction.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the category that owns the transaction.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    /**
     * Get the user who created the transaction.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include transactions for a specific school.
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope a query to only include income transactions.
     */
    public function scopeIncome($query)
    {
        return $query->where('transaction_type', 'income');
    }

    /**
     * Scope a query to only include expense transactions.
     */
    public function scopeExpense($query)
    {
        return $query->where('transaction_type', 'expense');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Get the transaction type badge class.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->transaction_type) {
            'income' => 'bg-success',
            'expense' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'completed' => 'bg-success',
            'pending' => 'bg-warning',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
