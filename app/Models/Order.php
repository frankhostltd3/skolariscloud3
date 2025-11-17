<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_type', // book|pamphlet
        'item_id',
        'item_title',
        'price',
        'buyer_name',
        'buyer_email',
        'status', // pending|paid|cancelled
        'payment_method',
        'paid_at',
        'receipt_email_sent_at',
        'admin_notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'paid_at' => 'datetime',
        'receipt_email_sent_at' => 'datetime',
    ];
}
