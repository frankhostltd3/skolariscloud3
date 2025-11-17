<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('type', 20)->default('general'); // general, announcement, alert, reminder
            $table->string('priority', 10)->default('normal'); // low, normal, high, urgent
            $table->json('target_audience')->nullable(); // ['admins', 'staff', 'teachers', 'students', 'parents']
            $table->json('specific_recipients')->nullable(); // specific user IDs
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->boolean('is_active')->default(true);
            $table->json('channels')->nullable(); // ['email', 'sms', 'whatsapp', 'system']
            $table->timestamps();

            $table->index(['type', 'priority']);
            $table->index('scheduled_at');
            $table->index('sent_at');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};