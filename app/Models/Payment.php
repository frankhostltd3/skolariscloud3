<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'receipt_number',
        'invoice_id',
        'student_id',
        'amount',
        'payment_method',
        'payment_date',
        'reference_number',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Get the school that owns the payment.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the invoice that owns the payment.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the student that owns the payment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the user who received the payment.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Scope a query to only include payments for a specific school.
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
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by payment method.
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Get the payment method label.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'Cash',
            'card' => 'Credit/Debit Card',
            'bank_transfer' => 'Bank Transfer',
            'check' => 'Check',
            'mobile_money' => 'Mobile Money',
            default => ucfirst($this->payment_method),
        };
    }
}
