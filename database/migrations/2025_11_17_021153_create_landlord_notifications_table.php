<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection(config('tenancy.database.central_connection'))
            ->create('landlord_notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('title');
                $table->text('message');
                $table->string('channel')->default('system'); // system|email|sms|slack|webhook
                $table->json('audience')->nullable(); // filters e.g. plans, countries
                $table->json('meta')->nullable();
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();

                $table->index(['channel', 'scheduled_at']);
            });
    }

    public function down(): void
    {
        Schema::connection(config('tenancy.database.central_connection'))
            ->dropIfExists('landlord_notifications');
    }
};
