<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('integration_health_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('integration_slug');
            $table->string('display_name');
            $table->string('vendor')->nullable();
            $table->string('integration_type')->nullable();
            $table->string('region')->nullable();
            $table->string('environment')->default('production');
            $table->string('status')->default('unknown');
            $table->text('status_message')->nullable();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->unsignedInteger('throughput_per_minute')->default(0);
            $table->decimal('error_rate', 5, 2)->default(0);
            $table->decimal('uptime_percentage', 5, 2)->default(0);
            $table->unsignedInteger('active_automations')->default(0);
            $table->json('channels')->nullable();
            $table->json('metadata')->nullable();
            $table->string('source')->default('database');
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->unique(['integration_slug', 'region', 'environment'], 'integration_health_unique_slug_region_env');
            $table->index(['region', 'integration_type']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_health_snapshots');
    }
};
