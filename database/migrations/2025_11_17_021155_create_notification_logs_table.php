<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 20); // sms|whatsapp|email|system
            $table->string('message_type', 20)->default('text'); // text|media|template
            $table->string('to', 64);
            $table->string('provider', 50)->nullable();
            $table->string('status', 20)->default('sent'); // sent|failed|delivered|read
            $table->text('error')->nullable();
            $table->json('meta')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('target_type', 20)->nullable(); // student|teacher|parent|admin|staff
            $table->unsignedBigInteger('target_id')->nullable();
            $table->unsignedBigInteger('notification_id')->nullable(); // reference to notifications table
            $table->timestamps();

            $table->index(['channel', 'status']);
            $table->index(['target_type', 'target_id']);
            $table->index('created_by');
            $table->index('notification_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};