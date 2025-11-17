<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'default_days',
        'annual_entitlement',
        'accrual_type',
        'accrual_rate',
        'carry_forward_limit',
        'paid',
        'max_consecutive_days',
        'requires_approval',
        'description',
    ];

    protected $casts = [
        'default_days' => 'integer',
        'annual_entitlement' => 'integer',
        'accrual_rate' => 'decimal:2',
        'carry_forward_limit' => 'integer',
        'paid' => 'boolean',
        'max_consecutive_days' => 'integer',
        'requires_approval' => 'boolean',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
