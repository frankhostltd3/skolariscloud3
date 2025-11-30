<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DomainOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'domain_name',
        'domain_type',
        'tld',
        'contact_name',
        'contact_email',
        'contact_phone',
        'billing_entity',
        'billing_cycle',
        'amount',
        'currency',
        'payment_method',
        'payment_status',
        'payment_reference',
        'status',
        'purchase_notes',
        'admin_notes',
        'dns_assignee',
        'dns_records',
        'dns_verified',
        'dns_verified_at',
        'verification_token',
        'ssl_enabled',
        'ssl_provider',
        'ssl_status',
        'ssl_issued_at',
        'ssl_expires_at',
        'registrar',
        'registrar_order_id',
        'registered_at',
        'expires_at',
        'auto_renew',
        'renewal_notified_at',
        'routing_active',
        'activated_at',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'dns_records' => 'array',
        'dns_verified' => 'boolean',
        'dns_verified_at' => 'datetime',
        'ssl_enabled' => 'boolean',
        'ssl_issued_at' => 'datetime',
        'ssl_expires_at' => 'datetime',
        'registered_at' => 'datetime',
        'expires_at' => 'datetime',
        'auto_renew' => 'boolean',
        'renewal_notified_at' => 'datetime',
        'routing_active' => 'boolean',
        'activated_at' => 'datetime',
        'approved_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getFullDomainAttribute(): string
    {
        return $this->domain_name . $this->tld;
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        return $this->expires_at ? now()->diffInDays($this->expires_at, false) : null;
    }

    public function isExpiringSoon(): bool
    {
        return $this->expires_at && $this->expires_at->diffInDays(now()) <= 30;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function needsRenewal(): bool
    {
        return $this->auto_renew && $this->isExpiringSoon() && !$this->isExpired();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiring($query, int $days = 30)
    {
        return $query->where('expires_at', '<=', now()->addDays($days))
            ->where('expires_at', '>', now());
    }

    public function scopeNeedsRenewal($query)
    {
        return $query->where('auto_renew', true)
            ->expiring(30);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-warning',
            'reviewing' => 'bg-info',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'active' => 'bg-success',
            'expired' => 'bg-secondary',
            'cancelled' => 'bg-dark',
            default => 'bg-secondary',
        };
    }

    public function getPaymentStatusBadgeClassAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'bg-warning',
            'paid' => 'bg-success',
            'failed' => 'bg-danger',
            'refunded' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }
}
