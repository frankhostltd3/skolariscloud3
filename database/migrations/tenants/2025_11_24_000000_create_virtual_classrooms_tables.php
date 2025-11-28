<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Virtual Classes
        Schema::create('virtual_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('platform')->default('zoom'); // zoom, google_meet, microsoft_teams, etc.
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();
            $table->string('meeting_url')->nullable();
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled');
            $table->string('recording_url')->nullable();
            $table->boolean('auto_record')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable(); // daily, weekly, etc.
            $table->date('recurrence_end_date')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['teacher_id', 'scheduled_at']);
            $table->index(['class_id', 'status']);
        });

        // Virtual Class Attendances
        Schema::create('virtual_class_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_class_id')->constrained('virtual_classes')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('joined_at');
            $table->dateTime('left_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->enum('status', ['present', 'late', 'absent'])->default('present'); // Added status column based on controller usage
            $table->timestamps();
            $table->softDeletes();

            $table->index(['virtual_class_id', 'student_id']);
        });

        // Learning Materials
        Schema::create('learning_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('document'); // document, video, audio, image, link, youtube
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_mime')->nullable();
            $table->string('external_url')->nullable();
            $table->string('youtube_id')->nullable();
            $table->boolean('is_downloadable')->default(true);
            $table->integer('views_count')->default(0);
            $table->integer('downloads_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['class_id', 'type']);
            $table->index(['teacher_id', 'created_at']);
        });

        // Material Accesses
        Schema::create('material_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_material_id')->constrained('learning_materials')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('action')->default('view'); // view, download
            $table->dateTime('accessed_at');
            // No timestamps as per model, but SoftDeletes is used
            $table->softDeletes();

            $table->index(['learning_material_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_accesses');
        Schema::dropIfExists('learning_materials');
        Schema::dropIfExists('virtual_class_attendances');
        Schema::dropIfExists('virtual_classes');
    }
};
