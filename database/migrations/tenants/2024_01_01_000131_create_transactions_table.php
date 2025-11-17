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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('transaction_type'); // income, expense
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->foreignId('category_id')->nullable()->constrained('expense_categories')->onDelete('set null');
            $table->string('payment_method')->nullable(); // cash, card, bank_transfer, check
            $table->string('reference_number')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('transaction_date');
            $table->string('status')->default('completed'); // pending, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'transaction_date']);
            $table->index(['school_id', 'transaction_type']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
