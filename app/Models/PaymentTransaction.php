<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'gateway_id',
        'transaction_id',
        'external_id',
        'request_id',
        'amount',
        'currency',
        'phone_number',
        'email',
        'customer_name',
        'payable_type',
        'payable_id',
        'description',
        'metadata',
        'status',
        'failure_reason',
        'failure_code',
        'provider_request',
        'provider_response',
        'callback_data',
        'initiated_at',
        'completed_at',
        'callback_received_at',
        'processing_time_ms',
        'initiated_by',
        'ip_address',
        'user_agent',
        // Legacy fields for backward compatibility
        'transaction_type',
        'related_id',
        'gateway',
        'reference',
        'payer_email',
        'payer_name',
        'payer_phone',
        'payment_url',
        'raw_request',
        'raw_response',
        'webhook_data',
        'failed_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'provider_request' => 'array',
        'provider_response' => 'array',
        'callback_data' => 'array',
        'raw_request' => 'array',
        'raw_response' => 'array',
        'webhook_data' => 'array',
        'initiated_at' => 'datetime',
        'completed_at' => 'datetime',
        'callback_received_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function mobileMoneyGateway(): BelongsTo
    {
        return $this->belongsTo(MobileMoneyGateway::class, 'gateway_id');
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Legacy relationship for backward compatibility
     */
    public function relatable(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'transaction_type', 'related_id');
    }

    // ===========================
    // SCOPES
    // ===========================

    public function scopeForSchool($query, $schoolId = null)
    {
        $schoolId = $schoolId ?? request()->attributes->get('currentSchool')?->id;
        return $query->where('school_id', $schoolId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForPhone($query, string $phone)
    {
        return $query->where('phone_number', $phone);
    }

    public function scopeForGateway($query, int $gatewayId)
    {
        return $query->where('gateway_id', $gatewayId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    // ===========================
    // STATUS HELPERS
    // ===========================

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function isFinalized(): bool
    {
        return in_array($this->status, ['completed', 'failed', 'cancelled', 'expired', 'refunded']);
    }

    public function canRetry(): bool
    {
        return in_array($this->status, ['failed', 'expired', 'cancelled']);
    }

    public function canRefund(): bool
    {
        return $this->status === 'completed' && !$this->isRefunded();
    }

    // ===========================
    // STATUS MUTATIONS
    // ===========================

    public function markAsProcessing(string $externalId = null, array $providerResponse = []): void
    {
        $this->update([
            'status' => 'processing',
            'external_id' => $externalId ?? $this->external_id,
            'provider_response' => $providerResponse ?: $this->provider_response,
        ]);
    }

    public function markAsCompleted(array $callbackData = []): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'callback_received_at' => now(),
            'callback_data' => $callbackData,
            'webhook_data' => $callbackData, // Legacy field
            'processing_time_ms' => $this->initiated_at 
                ? now()->diffInMilliseconds($this->initiated_at) 
                : null,
        ]);
    }

    public function markAsFailed(string $reason, string $code = null, array $callbackData = []): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'failure_code' => $code,
            'completed_at' => now(),
            'failed_at' => now(), // Legacy field
            'callback_received_at' => !empty($callbackData) ? now() : null,
            'callback_data' => $callbackData ?: $this->callback_data,
        ]);
    }

    public function markAsCancelled(string $reason = 'Cancelled by user'): void
    {
        $this->update([
            'status' => 'cancelled',
            'failure_reason' => $reason,
            'completed_at' => now(),
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update([
            'status' => 'expired',
            'failure_reason' => 'Payment request expired',
            'completed_at' => now(),
        ]);
    }

    public function markAsRefunded(array $refundData = []): void
    {
        $this->update([
            'status' => 'refunded',
            'callback_data' => array_merge($this->callback_data ?? [], ['refund' => $refundData]),
        ]);
    }

    // ===========================
    // TRANSACTION ID GENERATION
    // ===========================

    public static function generateTransactionId(string $prefix = 'TXN'): string
    {
        return $prefix . '-' . strtoupper(bin2hex(random_bytes(4))) . '-' . time();
    }

    public static function generateRequestId(): string
    {
        return 'REQ-' . now()->format('YmdHis') . '-' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    // ===========================
    // ATTRIBUTE ACCESSORS
    // ===========================

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-warning text-dark',
            'processing' => 'bg-info',
            'completed' => 'bg-success',
            'failed' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            'expired' => 'bg-dark',
            'refunded' => 'bg-purple',
            default => 'bg-secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bi-hourglass-split',
            'processing' => 'bi-arrow-repeat',
            'completed' => 'bi-check-circle-fill',
            'failed' => 'bi-x-circle-fill',
            'cancelled' => 'bi-slash-circle',
            'expired' => 'bi-clock-history',
            'refunded' => 'bi-arrow-counterclockwise',
            default => 'bi-question-circle',
        };
    }

    public function getProviderNameAttribute(): string
    {
        return $this->mobileMoneyGateway?->name ?? $this->gateway ?? 'Unknown';
    }

    public function getProcessingDurationAttribute(): ?string
    {
        if (!$this->processing_time_ms) {
            return null;
        }

        if ($this->processing_time_ms < 1000) {
            return $this->processing_time_ms . 'ms';
        }

        return round($this->processing_time_ms / 1000, 2) . 's';
    }

    // ===========================
    // UTILITY METHODS
    // ===========================

    /**
     * Get a summary for display
     */
    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'amount' => $this->formatted_amount,
            'phone' => $this->phone_number ?? $this->payer_phone,
            'email' => $this->email ?? $this->payer_email,
            'status' => $this->status,
            'gateway' => $this->provider_name,
            'created' => $this->created_at->diffForHumans(),
            'completed' => $this->completed_at?->diffForHumans(),
        ];
    }

    /**
     * Get audit trail data
     */
    public function getAuditTrail(): array
    {
        return [
            'transaction_id' => $this->transaction_id,
            'external_id' => $this->external_id,
            'request_id' => $this->request_id,
            'initiated_by' => $this->initiator?->name,
            'initiated_at' => $this->initiated_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'processing_time' => $this->processing_duration,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
        ];
    }

    /**
     * Expire pending transactions older than given minutes
     */
    public static function expireOldPending(int $minutes = 30): int
    {
        return static::pending()
            ->where('created_at', '<', now()->subMinutes($minutes))
            ->update([
                'status' => 'expired',
                'failure_reason' => 'Payment request expired after ' . $minutes . ' minutes',
                'completed_at' => now(),
            ]);
    }

    /**
     * Find transaction by various identifiers
     */
    public static function findByIdentifier(string $identifier): ?static
    {
        return static::where('transaction_id', $identifier)
            ->orWhere('external_id', $identifier)
            ->orWhere('request_id', $identifier)
            ->orWhere('reference', $identifier)
            ->first();
    }
}
