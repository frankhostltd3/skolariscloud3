<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'successful',
        'tenant_id',
        'attempted_at',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    /**
     * Get recent failed attempts for an email/tenant
     */
    public static function recentFailedAttempts(string $email, ?string $tenantId = null, int $minutes = 15): int
    {
        return static::where('email', $email)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('successful', false)
            ->where('attempted_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Log a login attempt
     */
    public static function logAttempt(string $email, bool $successful = false): void
    {
        static::create([
            'email' => $email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'successful' => $successful,
            'tenant_id' => tenant('id'),
            'attempted_at' => now(),
        ]);
    }

    /**
     * Clear old login attempts (cleanup)
     */
    public static function clearOldAttempts(int $days = 30): int
    {
        return static::where('attempted_at', '<', now()->subDays($days))->delete();
    }
}
