<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'mailer',
        'from_name',
        'from_address',
        'config',
    ];

    protected $casts = [
        'config' => 'encrypted:array',
    ];
}
