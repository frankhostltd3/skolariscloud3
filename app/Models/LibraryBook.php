<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class LibraryBook extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'category',
        'quantity',
        'available_quantity',
        'publisher',
        'publication_year',
        'description',
        'short_description',
        'cover_image',
        'cover_image_path',
        'location',
        'status',
        'purchase_price',
        'language',
        'pages',
        // Bookstore fields
        'is_for_sale',
        'sale_price',
        'stock_quantity',
        'is_featured',
        'sold_count',
        'discount_percentage',
        // Digital Product fields
        'is_digital',
        'digital_file_path',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'quantity' => 'integer',
        'available_quantity' => 'integer',
        'purchase_price' => 'decimal:2',
        'pages' => 'integer',
        // Bookstore casts
        'is_for_sale' => 'boolean',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_featured' => 'boolean',
        'sold_count' => 'integer',
        'discount_percentage' => 'decimal:2',
        'is_digital' => 'boolean',
    ];

    /**
     * Get all transactions for this book
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(LibraryTransaction::class);
    }

    /**
     * Get active borrowed transactions
     */
    public function activeBorrows(): HasMany
    {
        return $this->transactions()->where('status', 'borrowed');
    }

    /**
     * Check if book is available for borrowing
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->available_quantity > 0;
    }

    /**
     * Get borrowing rate
     */
    public function getBorrowingRateAttribute(): float
    {
        if ($this->quantity == 0) {
            return 0;
        }

        return (($this->quantity - $this->available_quantity) / $this->quantity) * 100;
    }

    /**
     * Scope: Available books
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'available')
                    ->where('available_quantity', '>', 0);
    }

    /**
     * Scope: Search books
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('author', 'like', "%{$search}%")
              ->orWhere('isbn', 'like', "%{$search}%")
              ->orWhere('publisher', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Filter by category
     */
    public function scopeByCategory(Builder $query, ?string $category): Builder
    {
        if (!$category) {
            return $query;
        }

        return $query->where('category', $category);
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus(Builder $query, ?string $status): Builder
    {
        if (!$status) {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Scope: Books for sale
     */
    public function scopeForSale(Builder $query): Builder
    {
        return $query->where('is_for_sale', true)
                    ->where('stock_quantity', '>', 0);
    }

    /**
     * Scope: Featured books
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)
                    ->where('is_for_sale', true);
    }

    /**
     * Scope: In stock books
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Scope: Bestsellers
     */
    public function scopeBestsellers(Builder $query, int $limit = 10): Builder
    {
        return $query->where('is_for_sale', true)
                    ->orderBy('sold_count', 'desc')
                    ->limit($limit);
    }

    /**
     * Check if book is in stock for purchase
     */
    public function isInStock(): bool
    {
        return $this->is_for_sale && $this->stock_quantity > 0;
    }

    /**
     * Get final price after discount
     */
    public function getFinalPriceAttribute(): float
    {
        if (!$this->sale_price) {
            return 0;
        }

        $discount = ($this->sale_price * $this->discount_percentage) / 100;
        return $this->sale_price - $discount;
    }

    /**
     * Get discount amount
     */
    public function getDiscountAmountAttribute(): float
    {
        if (!$this->sale_price || !$this->discount_percentage) {
            return 0;
        }

        return ($this->sale_price * $this->discount_percentage) / 100;
    }

    /**
     * Get cover image URL
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if ($this->cover_image_path) {
            return asset('storage/' . $this->cover_image_path);
        }

        return null;
    }

    /**
     * Get bookstore order items
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(BookstoreOrderItem::class);
    }
}
