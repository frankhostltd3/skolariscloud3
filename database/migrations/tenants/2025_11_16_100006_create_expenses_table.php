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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('expense_categories')->onDelete('set null');
            $table->string('expense_name');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->string('payment_method')->nullable(); // cash, card, bank_transfer, check
            $table->string('reference_number')->nullable();
            $table->string('vendor')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamps();

            $table->index(['school_id', 'expense_date']);
            $table->index(['school_id', 'category_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
