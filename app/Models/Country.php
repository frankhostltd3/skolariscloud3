<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'region', 'name_translations',
    ];

    protected $casts = [
        'name_translations' => 'array',
    ];
}
