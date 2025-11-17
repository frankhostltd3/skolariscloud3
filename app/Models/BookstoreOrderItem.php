<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookstoreOrderItem extends Model
{
    protected $fillable = [
        'bookstore_order_id',
        'library_book_id',
        'quantity',
        'unit_price',
        'discount_percentage',
        'discount_amount',
        'subtotal',
        'book_title',
        'book_author',
        'book_isbn',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the order this item belongs to
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(BookstoreOrder::class, 'bookstore_order_id');
    }

    /**
     * Get the book
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(LibraryBook::class, 'library_book_id');
    }
}
