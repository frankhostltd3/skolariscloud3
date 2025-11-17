<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'discussion_id',
        'reply_id',
        'user_id',
    ];

    public $timestamps = true;

    /**
     * Relationships
     */
    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }

    public function reply()
    {
        return $this->belongsTo(DiscussionReply::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
