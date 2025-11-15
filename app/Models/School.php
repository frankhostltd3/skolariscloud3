<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Support\CentralDomain;

class School extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        'name',
        'code',
        'subdomain',
        'domain',
        'database',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(SchoolUserInvitation::class);
    }

    public function getUrlAttribute(): ?string
    {
        $host = $this->domain;

        if (! $host && $this->subdomain) {
            $baseDomain = CentralDomain::base();

            if ($baseDomain) {
                $host = $this->subdomain . '.' . $baseDomain;
            }
        }

        if (! $host) {
            return null;
        }

        $scheme = CentralDomain::scheme();
        $port = CentralDomain::port();

        $portSuffix = ($port && ! str_contains($host, ':')) ? ':' . $port : '';

        return $scheme . '://' . $host . $portSuffix;
    }

    /**
     * Get the school's logo URL from tenant settings.
     * Reads from the tenant database settings table.
     */
    public function getLogoUrlAttribute(): ?string
    {
        // Check if we're in a tenant context
        if (! config('database.connections.tenant.database')) {
            return null;
        }

        $logoPath = setting('school_logo');

        if ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath)) {
            return \Illuminate\Support\Facades\Storage::url($logoPath);
        }

        return null;
    }
}
