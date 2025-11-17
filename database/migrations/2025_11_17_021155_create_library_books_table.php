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
        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->unique()->nullable();
            $table->string('category')->default('General');
            $table->integer('quantity')->default(1);
            $table->integer('available_quantity')->default(1);
            $table->string('publisher')->nullable();
            $table->smallInteger('publication_year')->unsigned()->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('location')->nullable(); // Shelf/section location
            $table->enum('status', ['available', 'maintenance', 'lost', 'damaged'])->default('available');
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->string('language')->default('English');
            $table->integer('pages')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index('title');
            $table->index('author');
            $table->index('category');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_books');
    }
};
