<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'integration_slug',
        'region',
        'integration_type',
        'severity',
        'title',
        'detail',
        'occurred_at',
        'meta',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'meta' => 'array',
    ];

    public function scopeForRegion($query, ?string $region)
    {
        return $query->when($region, fn ($q) => $q->where('region', $region));
    }

    public function scopeForIntegrationType($query, ?string $type)
    {
        return $query->when($type, fn ($q) => $q->where('integration_type', $type));
    }
}
