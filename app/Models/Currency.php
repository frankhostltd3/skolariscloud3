<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'tenant';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'is_default',
        'is_active',
        'auto_update_enabled',
        'last_updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'auto_update_enabled' => 'boolean',
        'last_updated_at' => 'datetime',
    ];

    /**
     * Get the default currency for the tenant.
     *
     * @return Currency|null
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first()
            ?? static::where('code', 'USD')->first()
            ?? static::where('is_active', true)->first();
    }

    /**
     * Set this currency as the default.
     *
     * @return bool
     */
    public function setAsDefault()
    {
        // Remove default flag from all currencies
        static::query()->update(['is_default' => false]);

        // Set this currency as default
        $this->is_default = true;
        return $this->save();
    }

    /**
     * Format an amount with this currency.
     *
     * @param float $amount
     * @return string
     */
    public function format($amount)
    {
        return $this->symbol . number_format($amount, 2);
    }

    /**
     * Convert amount from this currency to another.
     *
     * @param float $amount
     * @param Currency $toCurrency
     * @return float
     */
    public function convertTo($amount, Currency $toCurrency)
    {
        // Convert to base currency first (divide by exchange rate)
        $baseAmount = $amount / $this->exchange_rate;

        // Then convert to target currency (multiply by its exchange rate)
        return $baseAmount * $toCurrency->exchange_rate;
    }

    /**
     * Scope to get only active currencies.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
