<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'type',
        'created_by',
        'last_message_at',
        'is_active',
        'participants',
    ];

    protected $casts = [
        'participants' => 'array',
        'last_message_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created this thread.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all messages in this thread.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'thread_id');
    }

    /**
     * Get the latest message in this thread.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class, 'thread_id')->latest();
    }

    /**
     * Scope for active threads.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for threads by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if a user is a participant in this thread.
     */
    public function hasParticipant($userId): bool
    {
        return in_array($userId, $this->participants ?? []);
    }

    /**
     * Add a participant to this thread.
     */
    public function addParticipant($userId): void
    {
        $participants = $this->participants ?? [];
        if (!in_array($userId, $participants)) {
            $participants[] = $userId;
            $this->update(['participants' => $participants]);
        }
    }

    /**
     * Remove a participant from this thread.
     */
    public function removeParticipant($userId): void
    {
        $participants = $this->participants ?? [];
        $participants = array_diff($participants, [$userId]);
        $this->update(['participants' => array_values($participants)]);
    }
}