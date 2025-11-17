<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class LibraryTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'library_book_id',
        'issued_by',
        'returned_to',
        'borrowed_at',
        'due_date',
        'returned_at',
        'status',
        'fine_amount',
        'fine_paid',
        'notes',
        'condition_notes',
        'renewal_count',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date' => 'datetime',
        'returned_at' => 'datetime',
        'fine_amount' => 'decimal:2',
        'fine_paid' => 'boolean',
        'renewal_count' => 'integer',
    ];

    /**
     * Get the user who borrowed the book
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the library book
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(LibraryBook::class, 'library_book_id');
    }

    /**
     * Get the staff who issued the book
     */
    public function issuedByStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Get the staff who received the return
     */
    public function returnedToStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_to');
    }

    /**
     * Check if transaction is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status !== 'returned' && 
               $this->due_date < now();
    }

    /**
     * Calculate fine amount based on overdue days
     */
    public function calculateFine(float $finePerDay = 1.0): float
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        $overdueDays = now()->diffInDays($this->due_date);
        return $overdueDays * $finePerDay;
    }

    /**
     * Get days overdue
     */
    public function getDaysOverdueAttribute(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }

    /**
     * Scope: Active borrows
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'borrowed');
    }

    /**
     * Scope: Overdue transactions
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'borrowed')
                    ->where('due_date', '<', now());
    }

    /**
     * Scope: For specific user
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: For specific book
     */
    public function scopeForBook(Builder $query, int $bookId): Builder
    {
        return $query->where('library_book_id', $bookId);
    }
}
