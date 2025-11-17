<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountLockout extends Model
{
    protected $fillable = [
        'email',
        'ip_address',
        'failed_attempts',
        'locked_until',
        'tenant_id',
    ];

    protected $casts = [
        'locked_until' => 'datetime',
        'failed_attempts' => 'integer',
    ];

    /**
     * Check if account is currently locked
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Get or create lockout record
     */
    public static function getOrCreate(string $email, ?string $tenantId = null): self
    {
        return static::firstOrCreate(
            [
                'email' => $email,
                'tenant_id' => $tenantId,
            ],
            [
                'ip_address' => request()->ip(),
                'failed_attempts' => 0,
            ]
        );
    }

    /**
     * Increment failed attempts and potentially lock account
     */
    public function incrementFailedAttempts(int $maxAttempts, int $lockoutMinutes): void
    {
        $this->increment('failed_attempts');
        $this->ip_address = request()->ip();

        if ($this->failed_attempts >= $maxAttempts) {
            $this->locked_until = now()->addMinutes($lockoutMinutes);
        }

        $this->save();
    }

    /**
     * Reset failed attempts after successful login
     */
    public function reset(): void
    {
        $this->update([
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Check if email is locked
     */
    public static function isEmailLocked(string $email, ?string $tenantId = null): bool
    {
        $lockout = static::where('email', $email)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->first();

        return $lockout && $lockout->isLocked();
    }

    /**
     * Get lockout details for email
     */
    public static function getLockout(string $email, ?string $tenantId = null): ?self
    {
        return static::where('email', $email)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->first();
    }
}
