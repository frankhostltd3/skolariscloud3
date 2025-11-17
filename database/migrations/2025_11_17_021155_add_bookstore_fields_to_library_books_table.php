<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('library_books', function (Blueprint $table) {
            // Bookstore specific fields
            $table->boolean('is_for_sale')->default(false)->after('pages');
            $table->decimal('sale_price', 10, 2)->nullable()->after('is_for_sale');
            $table->integer('stock_quantity')->default(0)->after('sale_price');
            $table->boolean('is_featured')->default(false)->after('stock_quantity');
            $table->string('cover_image_path')->nullable()->after('cover_image');
            
            // Additional useful fields for bookstore
            $table->text('short_description')->nullable()->after('description');
            $table->integer('sold_count')->default(0)->after('stock_quantity');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('sale_price');
            
            // Indexes for bookstore queries
            $table->index('is_for_sale');
            $table->index('is_featured');
            $table->index('sold_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('library_books', function (Blueprint $table) {
            $table->dropIndex(['is_for_sale']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['sold_count']);
            
            $table->dropColumn([
                'is_for_sale',
                'sale_price',
                'stock_quantity',
                'is_featured',
                'cover_image_path',
                'short_description',
                'sold_count',
                'discount_percentage',
            ]);
        });
    }
};
