<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('report_logs')) { return; }
        Schema::create('report_logs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('format')->default('csv');
            $table->json('parameters')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->unsignedInteger('rows_count')->default(0);
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('status')->default('completed');
            $table->text('error')->nullable();
            $table->timestamps();
            $table->index(['type','generated_at']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('report_logs');
    }
};

