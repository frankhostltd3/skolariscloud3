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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('receipt_number')->unique();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method'); // cash, card, bank_transfer, check, mobile_money
            $table->date('payment_date');
            $table->string('reference_number')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'student_id']);
            $table->index(['school_id', 'payment_date']);
            $table->index('receipt_number');
            $table->index('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
