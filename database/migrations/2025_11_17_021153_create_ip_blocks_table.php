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
        Schema::create('ip_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->index(); // Supports IPv4 and IPv6
            $table->string('reason')->nullable();
            $table->text('description')->nullable();
            $table->integer('violation_count')->default(0);
            $table->timestamp('blocked_at');
            $table->timestamp('expires_at')->nullable();
            $table->string('blocked_by')->nullable(); // auto or admin user ID
            $table->boolean('is_permanent')->default(false);
            $table->string('tenant_id')->nullable()->index();
            $table->timestamps();

            // Index for quick lookups
            $table->index(['ip_address', 'expires_at']);
            $table->index(['is_permanent', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_blocks');
    }
};
