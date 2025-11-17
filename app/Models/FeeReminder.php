<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_ids',
        'reminder_type',
        'target_audience',
        'class_id',
        'student_ids',
        'custom_message',
        'sent_via_email',
        'sent_via_sms',
        'recipient_count',
        'status',
        'error_message',
        'sent_at',
        'sent_by',
    ];

    protected $casts = [
        'fee_ids' => 'array',
        'student_ids' => 'array',
        'sent_via_email' => 'boolean',
        'sent_via_sms' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function targetClass(): BelongsTo
    {
        return $this->belongsTo(\App\Models\SchoolClass::class, 'class_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'sent_by');
    }

    public function fees()
    {
        return Fee::whereIn('id', $this->fee_ids ?? [])->get();
    }
}
