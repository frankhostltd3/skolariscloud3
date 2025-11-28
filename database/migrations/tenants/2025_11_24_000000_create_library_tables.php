<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->nullable();
            $table->string('category');
            $table->integer('quantity')->default(1);
            $table->integer('available_quantity')->default(1);
            $table->string('publisher')->nullable();
            $table->integer('publication_year')->nullable();
            $table->text('description')->nullable();
            $table->string('short_description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('available');
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->string('language')->nullable();
            $table->integer('pages')->nullable();

            // Bookstore fields
            $table->boolean('is_for_sale')->default(false);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->integer('sold_count')->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);

            $table->unsignedBigInteger('school_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('library_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('library_book_id')->constrained('library_books')->onDelete('cascade');
            $table->foreignId('issued_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('returned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('borrowed_at');
            $table->dateTime('due_date');
            $table->dateTime('returned_at')->nullable();
            $table->string('status')->default('borrowed');
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->boolean('fine_paid')->default(false);
            $table->text('notes')->nullable();
            $table->text('condition_notes')->nullable();
            $table->integer('renewal_count')->default(0);

            $table->unsignedBigInteger('school_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_transactions');
        Schema::dropIfExists('library_books');
    }
};
