<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LandlordInvoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'tenant_id',
        'tenant_name_snapshot',
        'tenant_plan_snapshot',
        'status',
        'auto_generated',
        'issued_at',
        'due_at',
        'period_start',
        'period_end',
        'subtotal',
        'tax_total',
        'discount_total',
        'total',
        'balance_due',
        'warning_level',
        'last_warning_sent_at',
        'suspension_at',
        'termination_at',
        'paid_at',
        'cancelled_at',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'auto_generated' => 'boolean',
        'issued_at' => 'date',
        'due_at' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'total' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'last_warning_sent_at' => 'datetime',
        'suspension_at' => 'datetime',
        'termination_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function getConnectionName(): ?string
    {
        return central_connection();
    }

    protected static function booted(): void
    {
        static::creating(function (LandlordInvoice $invoice): void {
            if (! $invoice->invoice_number) {
                $invoice->invoice_number = self::generateNextInvoiceNumber();
            }

            if ($invoice->status === null) {
                $invoice->status = 'draft';
            }

            if ($invoice->balance_due === null) {
                $invoice->balance_due = $invoice->total ?? 0;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LandlordInvoiceItem::class);
    }

    public function scopeForTenant(Builder $query, string $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function markSent(?\DateTimeInterface $sentAt = null): void
    {
        $this->issued_at = $sentAt?->format('Y-m-d') ?? now();
        $this->status = 'sent';
        $this->save();
    }

    public function markPaid(?\DateTimeInterface $paidAt = null): void
    {
        $this->paid_at = $paidAt ?? now();
        $this->status = 'paid';
        $this->balance_due = 0;
        $this->save();
    }

    public function markWarning(): void
    {
        $this->status = 'warning';
        $this->warning_level = ($this->warning_level ?? 0) + 1;
        $this->last_warning_sent_at = now();
        $metadata = $this->metadata ?? [];
        $metadata['enforcement_action'] = 'warning';
        $metadata['enforcement_logged_at'] = now()->toIso8601String();
        $this->metadata = $metadata;
        $this->save();
    }

    public function markSuspended(): void
    {
        $this->status = 'suspended';
        $this->suspension_at = now();
        $metadata = $this->metadata ?? [];
        $metadata['enforcement_action'] = 'suspended';
        $metadata['enforcement_logged_at'] = now()->toIso8601String();
        $this->metadata = $metadata;
        $this->save();
    }

    public function markTerminated(): void
    {
        $this->status = 'terminated';
        $this->termination_at = now();
        $metadata = $this->metadata ?? [];
        $metadata['enforcement_action'] = 'terminated';
        $metadata['enforcement_logged_at'] = now()->toIso8601String();
        $this->metadata = $metadata;
        $this->save();
    }

    public static function generateNextInvoiceNumber(): string
    {
        $prefix = 'LLI-' . now()->format('Y');
        $latestNumber = self::query()
            ->where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        if (! $latestNumber) {
            return $prefix . '-000001';
        }

        $lastSequence = (int) Str::afterLast($latestNumber, '-');
        return sprintf('%s-%06d', $prefix, $lastSequence + 1);
    }

    public function refreshFinancials(): void
    {
        $subtotal = $this->items()->sum('line_total');
        $this->subtotal = $subtotal;
        $this->total = $subtotal + ($this->tax_total ?? 0) - ($this->discount_total ?? 0);
        $paid = \Illuminate\Support\Arr::get($this->metadata ?? [], 'amount_paid', 0);
        $this->balance_due = $this->total - $paid;
        $this->save();
    }
}
