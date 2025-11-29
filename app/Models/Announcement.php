<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'type',
        'target_audience',
        'published_at',
        'expires_at',
        'author_id',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'target_audience' => 'array',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('published_at')->orWhere('published_at', '<=', now());
                     })
                     ->where(function ($q) {
                         $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                     });
    }

    public function scopeForAudience($query, $roles = [])
    {
        $roles = (array) $roles;

        // This is a simplified check. In a real app, you'd parse the JSON array.
        // For now, we assume if target_audience is null, it's for everyone.
        // Or we can implement a JSON contains check.
        return $query->where(function($q) use ($roles) {
            $q->whereNull('target_audience')
              ->orWhereJsonContains('target_audience', 'all');

            foreach ($roles as $role) {
                $q->orWhereJsonContains('target_audience', $role);
            }
        });
    }
}
