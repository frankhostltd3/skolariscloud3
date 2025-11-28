<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandlordInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_invoice_id',
        'line_type',
        'description',
        'category',
        'quantity',
        'unit_price',
        'line_total',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function getConnectionName(): ?string
    {
        return central_connection();
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(LandlordInvoice::class, 'landlord_invoice_id');
    }

    protected static function booted(): void
    {
        static::saving(function (LandlordInvoiceItem $item): void {
            $item->line_total = ($item->unit_price ?? 0) * ($item->quantity ?? 0);
        });

        static::saved(function (LandlordInvoiceItem $item): void {
            $item->invoice?->refreshFinancials();
        });

        static::deleted(function (LandlordInvoiceItem $item): void {
            $item->invoice?->refreshFinancials();
        });
    }
}
