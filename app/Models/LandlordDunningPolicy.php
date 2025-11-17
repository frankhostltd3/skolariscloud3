<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandlordDunningPolicy extends Model
{
    protected $table = 'landlord_dunning_policies';

    protected $fillable = [
        'name',
        'warning_threshold_days',
        'suspension_grace_days',
        'termination_grace_days',
        'reminder_windows',
        'late_fee_percent',
        'late_fee_flat',
        'warning_channels',
        'suspension_channels',
        'termination_channels',
        'warning_recipients',
        'suspension_recipients',
        'termination_recipients',
    'warning_phones',
    'suspension_phones',
    'termination_phones',
        'warning_webhooks',
        'suspension_webhooks',
        'termination_webhooks',
        'warning_slack_webhooks',
        'suspension_slack_webhooks',
        'termination_slack_webhooks',
        'templates',
        'is_active',
    ];

    protected $casts = [
        'reminder_windows' => 'array',
        'warning_channels' => 'array',
        'suspension_channels' => 'array',
        'termination_channels' => 'array',
        'warning_recipients' => 'array',
        'suspension_recipients' => 'array',
        'termination_recipients' => 'array',
    'warning_phones' => 'array',
    'suspension_phones' => 'array',
    'termination_phones' => 'array',
        'warning_webhooks' => 'array',
        'suspension_webhooks' => 'array',
        'termination_webhooks' => 'array',
        'warning_slack_webhooks' => 'array',
        'suspension_slack_webhooks' => 'array',
        'termination_slack_webhooks' => 'array',
        'templates' => 'array',
        'is_active' => 'boolean',
    ];

    public function getConnectionName(): ?string
    {
        return config('tenancy.database.central_connection');
    }

    public static function current(): self
    {
        return static::where('is_active', true)->first() ?? static::create([
            'name' => 'Default Policy',
            'warning_threshold_days' => (int) config('skolaris.billing.warning_threshold_days', 5),
            'suspension_grace_days' => (int) config('skolaris.billing.suspension_grace_days', 7),
            'termination_grace_days' => (int) config('skolaris.billing.termination_grace_days', 30),
            'reminder_windows' => [-7, -3, -1, 0, 3],
            'warning_channels' => config('skolaris.billing.warning_channels', ['mail']),
            'suspension_channels' => config('skolaris.billing.suspension_channels', ['mail']),
            'termination_channels' => config('skolaris.billing.termination_channels', ['mail']),
            'warning_recipients' => array_filter([
                config('skolaris.billing.warning_recipient'),
                ...config('skolaris.billing.warning_recipients', []),
            ]),
            'suspension_recipients' => array_filter([
                config('skolaris.billing.suspension_recipient'),
                ...config('skolaris.billing.suspension_recipients', []),
            ]),
            'termination_recipients' => array_filter([
                config('skolaris.billing.termination_recipient'),
                ...config('skolaris.billing.termination_recipients', []),
            ]),
            'templates' => [],
            'is_active' => true,
        ]);
    }
}
