<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BillingPlan extends Model
{
    /**
     * The database connection name for the model.
     * Always use the central database for landlord billing plans.
     *
     * @var string|null
     */
    protected $connection = 'mysql';

    /**
     * Use the configured central database connection so plans are shared across tenants.
     */
    public function getConnectionName(): ?string
    {
        return 'mysql'; // Force central database
    }

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'description',
        'price_amount',
        'price_display',
        'currency',
        'billing_period',
        'billing_period_label',
        'cta_label',
        'is_highlighted',
        'is_active',
        'position',
        'features',
        'modules',
        'limits',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'price_amount' => 'decimal:2',
        'is_highlighted' => 'boolean',
        'is_active' => 'boolean',
        'position' => 'integer',
        'features' => 'array',
        'modules' => 'array',
        'limits' => 'array',
    ];

    /**
     * Scope a query to only include active plans.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order plans by the configured position.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }

    /**
     * Get the formatted price label shown to users.
     */
    public function getDisplayPriceAttribute(): string
    {
        if ($this->price_display !== null && $this->price_display !== '') {
            return $this->price_display;
        }

        if ($this->price_amount === null) {
            return __('Contact us');
        }

        $currency = $this->currency ?? 'USD';

        return match ($currency) {
            'USD' => '$' . number_format((float) $this->price_amount, 0),
            'KES' => 'KSh ' . number_format((float) $this->price_amount),
            'NGN' => '₦' . number_format((float) $this->price_amount),
            'ZAR' => 'R' . number_format((float) $this->price_amount, 2),
            default => $currency . ' ' . number_format((float) $this->price_amount, 2),
        };
    }

    /**
     * Get the plan features with fallback to an empty array.
     *
     * @return array<int, string>
     */
    public function getFeaturesListAttribute(): array
    {
        $features = $this->features;

        if (! is_array($features)) {
            return [];
        }

        return array_values(array_filter($features, static fn ($value): bool => is_string($value) && trim($value) !== ''));
    }
}
