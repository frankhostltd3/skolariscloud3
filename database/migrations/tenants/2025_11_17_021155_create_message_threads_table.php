<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('message_threads', function (Blueprint $table) {
            $table->id();
            $table->string('subject')->nullable();
            $table->string('type', 20)->default('direct'); // direct, group, announcement
            $table->unsignedBigInteger('created_by');
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('participants')->nullable(); // array of user IDs
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index('created_by');
            $table->index('last_message_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_threads');
    }
};