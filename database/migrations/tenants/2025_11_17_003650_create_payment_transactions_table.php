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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type')->default('invoice'); // 'invoice' or 'fee'
            $table->unsignedBigInteger('related_id'); // invoice_id or fee_payment_id
            $table->string('gateway', 50); // paypal, mpesa, flutterwave, etc.
            $table->string('transaction_id')->unique(); // Gateway's transaction ID
            $table->string('reference')->nullable(); // Internal reference
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3);
            $table->string('status', 20)->default('pending'); // pending, processing, completed, failed, cancelled, refunded
            $table->string('payer_email')->nullable();
            $table->string('payer_name')->nullable();
            $table->string('payer_phone')->nullable();
            $table->text('description')->nullable();
            $table->text('payment_url')->nullable(); // Redirect URL for payment
            $table->text('raw_request')->nullable(); // JSON encoded request data
            $table->text('raw_response')->nullable(); // JSON encoded response data
            $table->text('webhook_data')->nullable(); // JSON encoded webhook callback data
            $table->timestamp('initiated_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['transaction_type', 'related_id']);
            $table->index('gateway');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
