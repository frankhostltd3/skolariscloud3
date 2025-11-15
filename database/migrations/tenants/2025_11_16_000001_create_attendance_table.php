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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
            $table->foreignId('class_stream_id')->nullable()->constrained('class_streams')->onDelete('set null');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('attendance_date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->enum('attendance_type', ['classroom', 'exam', 'event', 'general'])->default('classroom');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('school_id');
            $table->index('class_id');
            $table->index('attendance_date');
            $table->index(['school_id', 'attendance_date']);
            $table->index(['class_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
