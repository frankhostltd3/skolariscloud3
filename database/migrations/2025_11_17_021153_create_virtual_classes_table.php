<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('platform', ['zoom', 'google_meet', 'microsoft_teams', 'youtube_live', 'custom'])->default('zoom');
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();
            $table->string('meeting_url')->nullable();
            $table->text('join_instructions')->nullable();
            $table->dateTime('scheduled_at');
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled');
            $table->string('recording_url')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable(); // daily, weekly, monthly
            $table->date('recurrence_end_date')->nullable();
            $table->boolean('auto_record')->default(false);
            $table->boolean('notify_students')->default(true);
            $table->integer('max_participants')->nullable();
            $table->json('settings')->nullable(); // Additional platform-specific settings
            $table->timestamps();
            $table->softDeletes();

            $table->index(['teacher_id', 'scheduled_at']);
            $table->index(['class_id', 'status']);
        });

        // Attendance tracking for virtual classes
        Schema::create('virtual_class_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_class_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('joined_at')->nullable();
            $table->dateTime('left_at')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->boolean('was_present')->default(false);
            $table->timestamps();

            $table->unique(['virtual_class_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_class_attendances');
        Schema::dropIfExists('virtual_classes');
    }
};
