<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscussionReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'discussion_id',
        'user_id',
        'parent_id',
        'content',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    protected $appends = ['likes_count'];

    /**
     * Relationships
     */
    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(DiscussionReply::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(DiscussionReply::class, 'parent_id');
    }

    public function likes()
    {
        return $this->hasMany(DiscussionLike::class, 'reply_id');
    }

    /**
     * Scopes
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Accessors
     */
    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    /**
     * Methods
     */
    public function approve()
    {
        $this->update(['is_approved' => true]);
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function like($userId)
    {
        if (!$this->isLikedBy($userId)) {
            return $this->likes()->create(['user_id' => $userId]);
        }
        return false;
    }

    public function unlike($userId)
    {
        return $this->likes()->where('user_id', $userId)->delete();
    }
}
