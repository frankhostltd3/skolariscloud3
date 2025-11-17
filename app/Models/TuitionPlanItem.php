<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TuitionPlanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tuition_plan_id',
        'fee_id',
        'description',
        'quantity',
        'unit_price',
        'total_amount',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'net_amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tuition plan this item belongs to.
     */
    public function tuitionPlan(): BelongsTo
    {
        return $this->belongsTo(TuitionPlan::class);
    }

    /**
     * Get the fee this item is based on.
     */
    public function fee(): BelongsTo
    {
        return $this->belongsTo(Fee::class);
    }

    /**
     * Get the formatted unit price.
     */
    protected function formattedUnitPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->unit_price, 2)
        );
    }

    /**
     * Get the formatted total amount.
     */
    protected function formattedTotalAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->total_amount, 2)
        );
    }

    /**
     * Get the formatted net amount.
     */
    protected function formattedNetAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->net_amount, 2)
        );
    }

    /**
     * Calculate the net amount based on total, tax, and discount.
     */
    public function calculateNetAmount(): float
    {
        return $this->total_amount + $this->tax_amount - $this->discount_amount;
    }

    /**
     * Update the net amount.
     */
    public function updateNetAmount(): void
    {
        $this->net_amount = $this->calculateNetAmount();
        $this->save();
    }
}