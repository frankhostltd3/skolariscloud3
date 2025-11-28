<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected function centralConnection(): string
    {
        return config(
            'tenancy.database.central_connection',
            config('database.central_connection', config('database.default'))
        );
    }

    public function up(): void
    {
        Schema::connection($this->centralConnection())
            ->create('landlord_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action');
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->json('context')->nullable();
                $table->timestamps();

                $table->index(['action', 'created_at']);
                $table->index('user_id');
            });
    }

    public function down(): void
    {
        Schema::connection($this->centralConnection())
            ->dropIfExists('landlord_audit_logs');
    }
};
