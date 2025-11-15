<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessagingChannelSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'provider',
        'is_enabled',
        'config',
        'meta',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'config' => 'encrypted:array',
        'meta' => 'encrypted:array',
    ];
}
