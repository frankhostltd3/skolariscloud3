<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'invoice_number',
        'student_id',
        'fee_structure_id',
        'total_amount',
        'paid_amount',
        'balance',
        'issue_date',
        'due_date',
        'status',
        'academic_year',
        'term',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Get the school that owns the invoice.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the student that owns the invoice.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the fee structure that owns the invoice.
     */
    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    /**
     * Get the payments for the invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope a query to only include invoices for a specific school.
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope a query to only include unpaid invoices.
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['unpaid', 'partial']);
    }

    /**
     * Scope a query to only include overdue invoices.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                     ->orWhere(function($q) {
                         $q->whereIn('status', ['unpaid', 'partial'])
                           ->where('due_date', '<', now());
                     });
    }

    /**
     * Scope a query to filter by student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Get the days overdue.
     */
    public function getDaysOverdueAttribute(): int
    {
        if ($this->status === 'paid' || !$this->due_date) {
            return 0;
        }

        return max(0, now()->diffInDays($this->due_date, false) * -1);
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'paid' => 'bg-success',
            'partial' => 'bg-info',
            'unpaid' => 'bg-warning',
            'overdue' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
