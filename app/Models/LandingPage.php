<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'title', 'content', 'meta_title', 'meta_description', 'is_active', 'is_published'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
