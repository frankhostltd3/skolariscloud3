<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExaminationBody extends Model
{
    use HasFactory;

    protected $fillable = ['name','name_translations','code','country','meta','is_current'];

    protected $casts = [
        'meta' => 'array',
        'name_translations' => 'array',
        'is_current' => 'boolean',
    ];
}
