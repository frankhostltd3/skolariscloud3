<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discussion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'title',
        'content',
        'type',
        'is_pinned',
        'is_locked',
        'allow_replies',
        'requires_approval',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'allow_replies' => 'boolean',
        'requires_approval' => 'boolean',
    ];

    protected $appends = ['type_label', 'replies_count', 'likes_count'];

    /**
     * Relationships
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function replies()
    {
        return $this->hasMany(DiscussionReply::class)->whereNull('parent_id');
    }

    public function allReplies()
    {
        return $this->hasMany(DiscussionReply::class);
    }

    public function likes()
    {
        return $this->hasMany(DiscussionLike::class);
    }

    /**
     * Scopes
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_locked', false);
    }

    /**
     * Accessors
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'general' => 'General Discussion',
            'question' => 'Question',
            'announcement' => 'Announcement',
            'poll' => 'Poll',
            default => 'Unknown'
        };
    }

    public function getRepliesCountAttribute()
    {
        return $this->allReplies()->count();
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    /**
     * Methods
     */
    public function pin()
    {
        $this->update(['is_pinned' => true]);
    }

    public function unpin()
    {
        $this->update(['is_pinned' => false]);
    }

    public function lock()
    {
        $this->update(['is_locked' => true]);
    }

    public function unlock()
    {
        $this->update(['is_locked' => false]);
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
