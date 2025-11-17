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
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->boolean('successful')->default(false);
            $table->string('tenant_id', 36)->nullable()->index();
            $table->timestamp('attempted_at')->useCurrent();
            $table->timestamps();
            
            // Index for quick lookups
            $table->index(['email', 'tenant_id', 'attempted_at']);
            $table->index(['ip_address', 'attempted_at']);
        });
        
        // Create account lockouts table
        Schema::create('account_lockouts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('ip_address', 45)->nullable();
            $table->integer('failed_attempts')->default(0);
            $table->timestamp('locked_until')->nullable()->index();
            $table->string('tenant_id', 36)->nullable()->index();
            $table->timestamps();
            
            $table->unique(['email', 'tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_lockouts');
        Schema::dropIfExists('login_attempts');
    }
};
