<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityAuditLog extends Model
{
    protected $fillable = [
        'event_type',
        'email',
        'user_id',
        'ip_address',
        'user_agent',
        'description',
        'metadata',
        'severity',
        'tenant_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Event types
     */
    const EVENT_LOGIN_SUCCESS = 'login_success';
    const EVENT_LOGIN_FAILED = 'login_failed';
    const EVENT_LOGOUT = 'logout';
    const EVENT_PASSWORD_CHANGED = 'password_changed';
    const EVENT_PASSWORD_RESET_REQUESTED = 'password_reset_requested';
    const EVENT_PASSWORD_RESET_COMPLETED = 'password_reset_completed';
    const EVENT_PASSWORD_RESET = 'password_reset'; // Admin password reset
    const EVENT_USER_CREATED = 'user_created'; // User account created
    const EVENT_ACCOUNT_LOCKED = 'account_locked';
    const EVENT_ACCOUNT_UNLOCKED = 'account_unlocked';
    const EVENT_TWO_FACTOR_ENABLED = 'two_factor_enabled';
    const EVENT_TWO_FACTOR_DISABLED = 'two_factor_disabled';
    const EVENT_SETTINGS_CHANGED = 'settings_changed';
    const EVENT_PERMISSION_CHANGED = 'permission_changed';
    
    /**
     * Severity levels
     */
    const SEVERITY_INFO = 'info';
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_CRITICAL = 'critical';

    /**
     * Get the user that owns the log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a security event
     */
    public static function logEvent(
        string $eventType,
        ?string $email = null,
        ?int $userId = null,
        ?string $description = null,
        array $metadata = [],
        string $severity = self::SEVERITY_INFO
    ): void {
        static::create([
            'event_type' => $eventType,
            'email' => $email,
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
            'metadata' => $metadata,
            'severity' => $severity,
            'tenant_id' => tenant('id'),
        ]);
    }

    /**
     * Get logs for a specific user
     */
    public static function forUser(int $userId, int $limit = 50)
    {
        return static::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent security events
     */
    public static function recent(int $limit = 100, ?string $tenantId = null)
    {
        return static::query()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get critical security events
     */
    public static function critical(int $days = 7, ?string $tenantId = null)
    {
        return static::query()
            ->where('severity', self::SEVERITY_CRITICAL)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Clean up old audit logs (retention policy)
     */
    public static function cleanup(int $days = 90): int
    {
        $count = static::where('created_at', '<', now()->subDays($days))->count();
        static::where('created_at', '<', now()->subDays($days))->delete();
        return $count;
    }
}
