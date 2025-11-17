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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->index();
            $table->json('value')->nullable();
            $table->string('category')->default('general')->index();
            $table->boolean('is_public')->default(true);
            $table->string('tenant_id', 36)->nullable();
            $table->timestamps();

            $table->unique(['key', 'tenant_id']);
            $table->index(['category', 'tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
