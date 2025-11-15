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
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendance')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['present', 'absent', 'late', 'excused', 'sick', 'half_day'])->default('present');
            $table->time('arrival_time')->nullable();
            $table->time('departure_time')->nullable();
            $table->integer('minutes_late')->default(0);
            $table->text('excuse_reason')->nullable();
            $table->string('excuse_document')->nullable();
            $table->boolean('notified_parent')->default(false);
            $table->timestamp('notification_sent_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('attendance_id');
            $table->index('student_id');
            $table->index('status');
            $table->index(['student_id', 'attendance_id']);
            $table->unique(['attendance_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
