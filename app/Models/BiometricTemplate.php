<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BiometricTemplate extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'user_type',
        'user_id',
        'biometric_type',
        'finger_position',
        'template_data',
        'device_id',
        'quality_score',
        'enrolled_at',
        'enrolled_by',
        'is_active',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    public function enrolledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enrolled_by');
    }

    /**
     * Get finger name from position
     */
    public function getFingerNameAttribute(): ?string
    {
        if ($this->biometric_type !== 'fingerprint') {
            return null;
        }

        $fingers = [
            1 => 'Right Thumb',
            2 => 'Right Index',
            3 => 'Right Middle',
            4 => 'Right Ring',
            5 => 'Right Little',
            6 => 'Left Thumb',
            7 => 'Left Index',
            8 => 'Left Middle',
            9 => 'Left Ring',
            10 => 'Left Little',
        ];

        return $fingers[$this->finger_position] ?? 'Unknown';
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by biometric type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('biometric_type', $type);
    }
}
