<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('integration_events', function (Blueprint $table) {
            $table->id();
            $table->string('integration_slug')->nullable();
            $table->string('region')->nullable();
            $table->string('integration_type')->nullable();
            $table->string('severity')->default('info');
            $table->string('title');
            $table->text('detail')->nullable();
            $table->timestamp('occurred_at');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('occurred_at');
            $table->index(['region', 'integration_type']);
            $table->index('severity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_events');
    }
};
