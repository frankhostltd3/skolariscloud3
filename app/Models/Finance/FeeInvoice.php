<?php

namespace App\Models\Finance;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year',
        'term',
        'total_amount',
        'currency',
        'due_date',
        'status',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FeeInvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments()->where('status', 'confirmed')->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return max(0, (float)$this->total_amount - $this->paid_amount);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->balance > 0 && $this->due_date && $this->due_date->isPast();
    }

    public function getEffectiveStatusAttribute(): string
    {
        if ($this->balance <= 0) return 'paid';
        if ($this->is_overdue) return 'overdue';
        if ($this->paid_amount > 0) return 'partial';
        return 'pending';
    }
}