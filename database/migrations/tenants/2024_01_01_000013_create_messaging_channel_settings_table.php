<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messaging_channel_settings', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 50);
            $table->string('provider', 100);
            $table->boolean('is_enabled')->default(false);
            $table->json('config')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['channel', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messaging_channel_settings');
    }
};
