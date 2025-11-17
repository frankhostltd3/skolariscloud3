<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'base',
        'quote',
        'rate',
        'provider',
        'fetched_at',
    ];

    protected $casts = [
        'rate' => 'decimal:8',
        'fetched_at' => 'datetime',
    ];

    /**
     * Get the base currency relationship
     */
    public function baseCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'base', 'code');
    }

    /**
     * Get the quote currency relationship
     */
    public function quoteCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'quote', 'code');
    }

    /**
     * Scope to get rates for a specific base currency
     */
    public function scopeForBase($query, string $baseCurrency)
    {
        return $query->where('base', $baseCurrency);
    }

    /**
     * Scope to get rates for a specific quote currency
     */
    public function scopeForQuote($query, string $quoteCurrency)
    {
        return $query->where('quote', $quoteCurrency);
    }

    /**
     * Scope to get rates fetched within a certain time frame
     */
    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('fetched_at', '>=', Carbon::now()->subMinutes($minutes));
    }

    /**
     * Scope to get rates from a specific provider
     */
    public function scopeFromProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Get the exchange rate between two currencies
     */
    public static function getRate(string $from, string $to): ?float
    {
        // Direct rate
        $rate = self::where('base', $from)
                   ->where('quote', $to)
                   ->recent(60) // Within last hour
                   ->first();

        if ($rate) {
            return (float) $rate->rate;
        }

        // Inverse rate
        $inverseRate = self::where('base', $to)
                          ->where('quote', $from)
                          ->recent(60)
                          ->first();

        if ($inverseRate) {
            return 1 / (float) $inverseRate->rate;
        }

        return null;
    }

    /**
     * Convert amount from one currency to another
     */
    public static function convert(float $amount, string $from, string $to): ?float
    {
        $rate = self::getRate($from, $to);

        if ($rate === null) {
            return null;
        }

        return $amount * $rate;
    }

    /**
     * Check if rate is stale (older than specified minutes)
     */
    public function isStale(int $minutes = 60): bool
    {
        return $this->fetched_at === null ||
               $this->fetched_at->addMinutes($minutes)->isPast();
    }

    /**
     * Get formatted rate with timestamp
     */
    public function getFormattedRateAttribute(): string
    {
        return number_format($this->rate, 6) . ' (' . $this->fetched_at?->diffForHumans() . ')';
    }
}