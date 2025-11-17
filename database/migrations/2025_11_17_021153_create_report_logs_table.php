<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('report_logs', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // Human readable name
            $table->string('type');                 // Category (academic, attendance, financial, enrollment, custom)
            $table->string('format')->default('csv');
            $table->json('parameters')->nullable(); // Filter parameters
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('generated_at')->nullable(); // convenience
            $table->unsignedInteger('rows_count')->default(0);
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('status')->default('completed'); // completed|failed|running
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['type', 'generated_at']);
            $table->foreign('generated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_logs');
    }
};

