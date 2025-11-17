<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'category',
        'is_active',
        'due_date',
        'recurring_type', // one-time, monthly, quarterly, yearly, term-based
        'applicable_to', // all, specific_class, specific_student
        'class_id',
        'student_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(FeeAssignment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'related_id')
            ->where('transaction_type', 'fee_payment');
    }

    /**
     * Scopes
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now())
            ->where('is_active', true);
    }

    public function scopeUpcoming(Builder $query, int $days = 7): Builder
    {
        return $query->whereBetween('due_date', [now(), now()->addDays($days)])
            ->where('is_active', true);
    }

    public function scopeRecurringType(Builder $query, string $type): Builder
    {
        return $query->where('recurring_type', $type);
    }

    /**
     * Helper Methods
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->is_active;
    }

    public function isUpcoming(int $days = 7): bool
    {
        return $this->due_date 
            && $this->due_date->isFuture() 
            && $this->due_date->diffInDays(now()) <= $days
            && $this->is_active;
    }

    public function getTotalCollected(): float
    {
        try {
            return (float) $this->payments()
                ->where('status', 'completed')
                ->sum('amount');
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    public function getPendingAmount(): float
    {
        try {
            return (float) $this->payments()
                ->where('status', 'pending')
                ->sum('amount');
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    public function getAssignedStudentsCount(): int
    {
        return $this->assignments()
            ->where('is_active', true)
            ->count();
    }

    public function getStatusBadgeClass(): string
    {
        if (!$this->is_active) {
            return 'bg-secondary';
        }
        
        if ($this->isOverdue()) {
            return 'bg-danger';
        }
        
        if ($this->isUpcoming(3)) {
            return 'bg-warning';
        }
        
        return 'bg-success';
    }

    public function getStatusText(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }
        
        if ($this->isOverdue()) {
            return 'Overdue';
        }
        
        if ($this->isUpcoming(3)) {
            return 'Due Soon';
        }
        
        return 'Active';
    }

    public function getRecurringTypeLabel(): string
    {
        return match($this->recurring_type) {
            'one-time' => 'One-time',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
            'term-based' => 'Term-based',
            default => ucfirst($this->recurring_type),
        };
    }

    public function getApplicableToLabel(): string
    {
        return match($this->applicable_to) {
            'all' => 'All Students',
            'specific_class' => 'Specific Class',
            'specific_student' => 'Specific Student',
            default => ucfirst($this->applicable_to),
        };
    }
}
