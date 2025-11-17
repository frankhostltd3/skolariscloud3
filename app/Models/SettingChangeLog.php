<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingChangeLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_email',
        'setting_key',
        'old_value',
        'new_value',
        'category',
        'ip_address',
        'user_agent',
        'tenant_id',
    ];

    protected $casts = [
        'old_value' => 'json',
        'new_value' => 'json',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user who made the change
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a setting change
     */
    public static function logChange(
        string $key,
        mixed $oldValue,
        mixed $newValue,
        string $category = 'general',
        ?int $userId = null,
        ?string $userEmail = null
    ): self {
        /** @var \App\Models\User|null $authUser */
        $authUser = auth()->user();

        return static::create([
            'user_id' => $userId ?? auth()->id(),
            'user_email' => $userEmail ?? $authUser?->email,
            'setting_key' => $key,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'category' => $category,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'tenant_id' => tenant('id'),
        ]);
    }

    /**
     * Get recent changes
     */
    public static function recent(int $limit = 50)
    {
        return static::query()
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get changes by user
     */
    public static function byUser(int $userId, int $limit = 50)
    {
        return static::query()
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get changes by category
     */
    public static function byCategory(string $category, int $limit = 50)
    {
        return static::query()
            ->where('category', $category)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
