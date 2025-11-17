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
        Schema::create('library_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('library_book_id')->constrained()->onDelete('cascade');
            $table->foreignId('issued_by')->nullable()->constrained('users')->onDelete('set null'); // Staff who issued the book
            $table->foreignId('returned_to')->nullable()->constrained('users')->onDelete('set null'); // Staff who received return
            $table->timestamp('borrowed_at');
            $table->timestamp('due_date');
            $table->timestamp('returned_at')->nullable();
            $table->enum('status', ['borrowed', 'returned', 'overdue', 'lost'])->default('borrowed');
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->boolean('fine_paid')->default(false);
            $table->text('notes')->nullable();
            $table->text('condition_notes')->nullable(); // Condition when borrowed/returned
            $table->integer('renewal_count')->default(0);
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('user_id');
            $table->index('library_book_id');
            $table->index('status');
            $table->index('borrowed_at');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_transactions');
    }
};
