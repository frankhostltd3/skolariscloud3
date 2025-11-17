<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class IpBlock extends Model
{
    protected $fillable = [
        'ip_address',
        'reason',
        'description',
        'violation_count',
        'blocked_at',
        'expires_at',
        'blocked_by',
        'is_permanent',
        'tenant_id',
    ];

    protected $casts = [
        'blocked_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_permanent' => 'boolean',
    ];

    /**
     * Check if an IP address is currently blocked
     */
    public static function isBlocked(string $ipAddress, ?string $tenantId = null): bool
    {
        return static::query()
            ->where('ip_address', $ipAddress)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where(function ($query) {
                $query->where('is_permanent', true)
                    ->orWhere('expires_at', '>', now())
                    ->orWhereNull('expires_at');
            })
            ->exists();
    }

    /**
     * Get active block for an IP address
     */
    public static function getBlock(string $ipAddress, ?string $tenantId = null): ?self
    {
        return static::query()
            ->where('ip_address', $ipAddress)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where(function ($query) {
                $query->where('is_permanent', true)
                    ->orWhere('expires_at', '>', now())
                    ->orWhereNull('expires_at');
            })
            ->first();
    }

    /**
     * Block an IP address
     */
    public static function blockIp(
        string $ipAddress,
        string $reason = 'Multiple security violations',
        ?int $durationMinutes = null,
        bool $isPermanent = false,
        ?string $blockedBy = 'auto',
        ?string $tenantId = null
    ): self {
        $expiresAt = $isPermanent ? null : ($durationMinutes ? now()->addMinutes($durationMinutes) : now()->addHours(24));

        // Get or create block
        $block = static::firstOrNew([
            'ip_address' => $ipAddress,
            'tenant_id' => $tenantId,
        ]);

        $block->fill([
            'reason' => $reason,
            'blocked_at' => now(),
            'expires_at' => $expiresAt,
            'is_permanent' => $isPermanent,
            'blocked_by' => $blockedBy,
            'violation_count' => $block->exists ? $block->violation_count + 1 : 1,
        ]);

        $block->save();

        // Log the block
        SecurityAuditLog::logEvent(
            'ip_blocked',
            null,
            null,
            "IP address {$ipAddress} blocked",
            [
                'reason' => $reason,
                'duration_minutes' => $durationMinutes,
                'is_permanent' => $isPermanent,
                'blocked_by' => $blockedBy,
            ],
            SecurityAuditLog::SEVERITY_CRITICAL
        );

        return $block;
    }

    /**
     * Unblock an IP address
     */
    public static function unblockIp(string $ipAddress, ?string $tenantId = null): bool
    {
        $deleted = static::query()
            ->where('ip_address', $ipAddress)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->delete();

        if ($deleted) {
            SecurityAuditLog::logEvent(
                'ip_unblocked',
                null,
                null,
                "IP address {$ipAddress} unblocked",
                ['unblocked_by' => auth()->id() ?? 'system'],
                SecurityAuditLog::SEVERITY_INFO
            );
        }

        return $deleted > 0;
    }

    /**
     * Check if IP should be auto-blocked based on violations
     */
    public static function checkAutoBlock(string $ipAddress, ?string $tenantId = null): bool
    {
        $autoBlockThreshold = (int) setting('auto_block_threshold', 10);
        $autoBlockDuration = (int) setting('auto_block_duration', 1440); // 24 hours in minutes

        if (!setting('enable_auto_blocking', false)) {
            return false;
        }

        // Count failed login attempts from this IP in last hour
        $recentFailures = SecurityAuditLog::query()
            ->where('event_type', SecurityAuditLog::EVENT_LOGIN_FAILED)
            ->where('ip_address', $ipAddress)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('created_at', '>', now()->subHour())
            ->count();

        if ($recentFailures >= $autoBlockThreshold) {
            static::blockIp(
                $ipAddress,
                "Auto-blocked after {$recentFailures} failed login attempts",
                $autoBlockDuration,
                false,
                'auto',
                $tenantId
            );

            return true;
        }

        return false;
    }

    /**
     * Clean up expired blocks
     */
    public static function cleanupExpired(): int
    {
        return static::query()
            ->where('is_permanent', false)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->delete();
    }

    /**
     * Get all active blocks
     */
    public static function activeBlocks(?string $tenantId = null)
    {
        return static::query()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where(function ($query) {
                $query->where('is_permanent', true)
                    ->orWhere('expires_at', '>', now())
                    ->orWhereNull('expires_at');
            })
            ->orderBy('blocked_at', 'desc')
            ->get();
    }

    /**
     * Check if block is still active
     */
    public function isActive(): bool
    {
        if ($this->is_permanent) {
            return true;
        }

        if (!$this->expires_at) {
            return true;
        }

        return $this->expires_at->isFuture();
    }
}
