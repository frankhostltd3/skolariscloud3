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
        Schema::create('bookstore_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bookstore_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('library_book_id')->constrained()->onDelete('cascade');
            
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            
            // Store book snapshot in case book details change
            $table->string('book_title');
            $table->string('book_author')->nullable();
            $table->string('book_isbn')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('bookstore_order_id');
            $table->index('library_book_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookstore_order_items');
    }
};
