<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationHealthSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'integration_slug',
        'display_name',
        'vendor',
        'integration_type',
        'region',
        'environment',
        'status',
        'status_message',
        'latency_ms',
        'last_synced_at',
        'throughput_per_minute',
        'error_rate',
        'uptime_percentage',
        'active_automations',
        'channels',
        'metadata',
        'source',
        'display_order',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'channels' => 'array',
        'metadata' => 'array',
        'error_rate' => 'float',
        'uptime_percentage' => 'float',
    ];

    public function scopeForRegion($query, ?string $region)
    {
        return $query->when($region, fn ($q) => $q->where('region', $region));
    }

    public function scopeForIntegrationType($query, ?string $type)
    {
        return $query->when($type, fn ($q) => $q->where('integration_type', $type));
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'live' => 'success',
            'degraded' => 'warning',
            'incident', 'failed' => 'danger',
            'scheduled', 'maintenance' => 'info',
            default => 'secondary',
        };
    }

    public function getChannelBadgesAttribute(): array
    {
        return collect($this->channels ?? [])
            ->filter()
            ->values()
            ->all();
    }
}
