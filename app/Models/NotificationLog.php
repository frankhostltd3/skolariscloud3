<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'message_type',
        'to',
        'provider',
        'status',
        'error',
        'meta',
        'created_by',
        'target_type',
        'target_id',
        'notification_id',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Get the user who created this log entry.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the notification this log entry belongs to.
     */
    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    /**
     * Get the target user/model this notification was sent to.
     */
    public function target()
    {
        if ($this->target_type && $this->target_id) {
            return $this->target_type::find($this->target_id);
        }
        return null;
    }

    /**
     * Scope for logs by channel.
     */
    public function scopeByChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Scope for logs by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for successful logs.
     */
    public function scopeSuccessful($query)
    {
        return $query->whereIn('status', ['sent', 'delivered', 'read']);
    }

    /**
     * Scope for failed logs.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for logs by target type.
     */
    public function scopeForTargetType($query, $targetType)
    {
        return $query->where('target_type', $targetType);
    }

    /**
     * Check if this log entry represents a successful delivery.
     */
    public function isSuccessful(): bool
    {
        return in_array($this->status, ['sent', 'delivered', 'read']);
    }

    /**
     * Check if this log entry represents a failure.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}