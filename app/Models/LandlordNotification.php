<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandlordNotification extends Model
{
    use HasFactory;

    protected $table = 'landlord_notifications';

    protected $fillable = [
        'created_by', 'title', 'message', 'channel', 'audience', 'meta', 'scheduled_at', 'sent_at',
    ];

    protected $casts = [
        'audience' => 'array',
        'meta' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setConnection(config('tenancy.database.central_connection'));
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
