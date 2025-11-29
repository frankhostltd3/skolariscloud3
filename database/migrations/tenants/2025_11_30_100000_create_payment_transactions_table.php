<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Payment Transactions Table
     * 
     * Tracks all payment attempts, statuses, and provider responses.
     */
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('gateway_id')->constrained('mobile_money_gateways')->onDelete('cascade');
            
            // Transaction Identity
            $table->string('transaction_id')->unique();           // Our internal reference
            $table->string('external_id')->nullable()->index();   // Provider's transaction ID
            $table->string('request_id')->nullable();             // Provider's request ID (for polling)
            
            // Payment Details
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('UGX');
            $table->string('phone_number');                       // Customer phone (for mobile money)
            $table->string('email')->nullable();                  // Customer email
            $table->string('customer_name')->nullable();
            
            // Payment Context
            $table->string('payable_type')->nullable();           // Invoice, Order, etc.
            $table->unsignedBigInteger('payable_id')->nullable(); // Related model ID
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();                 // Additional context data
            
            // Status Tracking
            $table->enum('status', [
                'pending',      // Initiated, waiting for user
                'processing',   // User accepted, processing
                'completed',    // Successfully completed
                'failed',       // Failed (rejected, error)
                'cancelled',    // Cancelled by user/system
                'expired',      // Timed out
                'refunded',     // Money returned
            ])->default('pending');
            
            $table->string('failure_reason')->nullable();
            $table->string('failure_code')->nullable();
            
            // Provider Response
            $table->json('provider_request')->nullable();         // What we sent
            $table->json('provider_response')->nullable();        // What they returned
            $table->json('callback_data')->nullable();            // Webhook payload
            
            // Timing
            $table->timestamp('initiated_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('callback_received_at')->nullable();
            $table->integer('processing_time_ms')->nullable();    // Time to complete
            
            // User Context
            $table->foreignId('initiated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'created_at']);
            $table->index(['payable_type', 'payable_id']);
            $table->index('phone_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
