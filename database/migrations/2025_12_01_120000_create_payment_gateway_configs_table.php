<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Get the database connection for this migration.
     */
    public function getConnection(): ?string
    {
        return config('database.central_connection', config('database.default'));
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_gateway_configs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway', 50)->index();
            $table->string('context', 50)->default('landlord')->index(); // landlord, tenant
            $table->string('display_name')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_custom')->default(false);
            $table->boolean('is_test_mode')->default(true);
            $table->text('credentials')->nullable(); // Encrypted JSON
            $table->json('settings')->nullable();
            $table->json('custom_config')->nullable();
            $table->json('supported_currencies')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->unique(['gateway', 'context']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_configs');
    }
};
