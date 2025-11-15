<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('report_logs')) {
            Schema::create('report_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id');
                $table->unsignedBigInteger('user_id')->nullable(); // User who generated the report
                $table->string('name'); // Report filename
                $table->string('type'); // academic_performance, attendance_summary, financial_summary, enrollment_summary
                $table->enum('status', ['queued', 'running', 'completed', 'failed'])->default('queued');
                $table->integer('rows_count')->nullable();
                $table->bigInteger('size_bytes')->nullable();
                $table->string('file_path')->nullable(); // Storage path
                $table->text('error')->nullable(); // Error message if failed
                $table->timestamp('generated_at')->nullable();
                $table->timestamps();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

                $table->index('school_id');
                $table->index('user_id');
                $table->index('type');
                $table->index('status');
                $table->index('generated_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_logs');
    }
};
