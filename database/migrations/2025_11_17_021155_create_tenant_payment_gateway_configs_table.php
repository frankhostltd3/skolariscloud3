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
        // Tenant database
        Schema::create('tenant_payment_gateway_configs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway', 50); // paypal, flutterwave, pesapal, dpo, mtn_momo, airtel_money, mpesa
            $table->boolean('is_active')->default(false);
            $table->boolean('is_test_mode')->default(true);
            $table->text('credentials')->nullable(); // Encrypted JSON
            $table->json('settings')->nullable(); // Additional settings
            $table->json('supported_currencies')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->unique('gateway');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_payment_gateway_configs');
    }
};
