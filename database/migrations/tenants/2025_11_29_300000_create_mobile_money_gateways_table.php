<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mobile Money Gateway Configuration Table
     * 
     * This table stores mobile money gateway configurations for each tenant.
     * All sensitive credentials are encrypted before storage.
     * Supports any mobile money provider worldwide.
     */
    public function up(): void
    {
        Schema::create('mobile_money_gateways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            
            // Gateway Identity
            $table->string('name');                          // Display name (e.g., "MTN Mobile Money")
            $table->string('slug')->index();                 // Unique identifier (e.g., "mtn_momo_ug")
            $table->string('provider');                      // Provider type (e.g., "mtn_momo", "mpesa", "flutterwave", "custom")
            $table->string('country_code', 3)->nullable();   // ISO country code (e.g., "UG", "KE", "GH")
            $table->string('currency_code', 3)->nullable();  // ISO currency code (e.g., "UGX", "KES", "GHS")
            
            // API Configuration (Encrypted)
            $table->text('api_base_url')->nullable();        // Base API endpoint
            $table->text('public_key')->nullable();          // API Key / Public Key
            $table->text('secret_key')->nullable();          // Secret Key / API Secret
            $table->text('api_user')->nullable();            // API User ID (for MTN, etc.)
            $table->text('api_password')->nullable();        // API Password
            $table->text('subscription_key')->nullable();    // Subscription/Ocp-Apim key
            $table->text('webhook_secret')->nullable();      // Webhook verification secret
            $table->text('encryption_key')->nullable();      // For providers needing encryption
            
            // OAuth / Token Configuration
            $table->text('client_id')->nullable();           // OAuth Client ID
            $table->text('client_secret')->nullable();       // OAuth Client Secret
            $table->text('access_token')->nullable();        // Cached access token
            $table->timestamp('token_expires_at')->nullable();
            
            // Merchant/Business Details
            $table->string('merchant_id')->nullable();       // Merchant/Business ID
            $table->string('merchant_name')->nullable();     // Merchant display name
            $table->string('short_code')->nullable();        // Short code / Paybill number
            $table->string('till_number')->nullable();       // Till number (for some providers)
            $table->string('account_number')->nullable();    // Account number reference
            
            // Phone Number Configuration
            $table->string('sender_phone')->nullable();      // Sender phone for some APIs
            $table->string('callback_phone')->nullable();    // Callback notification phone
            
            // Environment & URLs
            $table->enum('environment', ['sandbox', 'production'])->default('sandbox');
            $table->text('callback_url')->nullable();        // Payment callback URL
            $table->text('return_url')->nullable();          // User return URL after payment
            $table->text('cancel_url')->nullable();          // Payment cancellation URL
            
            // Additional Custom Fields (JSON)
            $table->json('custom_fields')->nullable();       // Any provider-specific fields
            $table->json('supported_networks')->nullable();  // e.g., ["MTN", "Airtel", "Vodafone"]
            $table->json('fee_structure')->nullable();       // Transaction fee configuration
            
            // Status & Ordering
            $table->boolean('is_active')->default(false);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            
            // Metadata
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('support_email')->nullable();
            $table->string('support_phone')->nullable();
            
            // Testing
            $table->timestamp('last_tested_at')->nullable();
            $table->boolean('test_successful')->nullable();
            $table->text('test_message')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->unique(['school_id', 'slug']);
            $table->index(['school_id', 'is_active']);
            $table->index(['school_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_money_gateways');
    }
};
