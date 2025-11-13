<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class SchoolUserInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'email',
        'user_type',
        'token',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'user_type' => UserType::class,
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at instanceof Carbon && $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function markAccepted(): void
    {
        $this->forceFill(['accepted_at' => now()])->save();
    }
}
