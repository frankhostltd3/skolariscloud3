<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandlordAuditLog extends Model
{
    use HasFactory;

    protected $table = 'landlord_audit_logs';

    protected $connection = null; // set in constructor

    protected $fillable = [
        'user_id', 'action', 'ip_address', 'user_agent', 'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setConnection(config('tenancy.database.central_connection'));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
