<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

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
            $centralDomain = config('tenancy.central_domain');

            if ($centralDomain) {
                $host = $this->subdomain . '.' . ltrim($centralDomain, '.');
            }
        }

        return $host ? 'https://' . $host : null;
    }
}
