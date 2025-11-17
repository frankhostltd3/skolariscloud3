<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class TuitionPlanInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tuition_plan_id',
        'installment_number',
        'name',
        'description',
        'amount',
        'due_date',
        'is_paid',
        'paid_at',
        'payment_reference',
    ];

    protected $casts = [
        'installment_number' => 'integer',
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tuition plan this installment belongs to.
     */
    public function tuitionPlan(): BelongsTo
    {
        return $this->belongsTo(TuitionPlan::class);
    }

    /**
     * Get the formatted amount.
     */
    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->amount, 2)
        );
    }

    /**
     * Get the status of the installment.
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->is_paid) {
                    return 'paid';
                }

                if ($this->due_date->isPast()) {
                    return 'overdue';
                }

                return 'pending';
            }
        );
    }

    /**
     * Get the status badge class.
     */
    protected function statusBadgeClass(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match ($this->status) {
                    'paid' => 'badge bg-success',
                    'overdue' => 'badge bg-danger',
                    'pending' => 'badge bg-warning',
                    default => 'badge bg-secondary',
                };
            }
        );
    }

    /**
     * Get the status text.
     */
    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match ($this->status) {
                    'paid' => 'Paid',
                    'overdue' => 'Overdue',
                    'pending' => 'Pending',
                    default => 'Unknown',
                };
            }
        );
    }

    /**
     * Scope to get paid installments.
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    /**
     * Scope to get unpaid installments.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    /**
     * Scope to get overdue installments.
     */
    public function scopeOverdue($query)
    {
        return $query->where('is_paid', false)
                    ->where('due_date', '<', now());
    }

    /**
     * Scope to get pending installments.
     */
    public function scopePending($query)
    {
        return $query->where('is_paid', false)
                    ->where('due_date', '>=', now());
    }

    /**
     * Mark the installment as paid.
     */
    public function markAsPaid(?string $paymentReference = null): void
    {
        $this->is_paid = true;
        $this->paid_at = now();
        $this->payment_reference = $paymentReference;
        $this->save();
    }

    /**
     * Check if the installment is overdue.
     */
    public function isOverdue(): bool
    {
        return !$this->is_paid && $this->due_date->isPast();
    }

    /**
     * Get days until due date.
     */
    public function daysUntilDue(): int
    {
        return now()->diffInDays($this->due_date, false);
    }
}