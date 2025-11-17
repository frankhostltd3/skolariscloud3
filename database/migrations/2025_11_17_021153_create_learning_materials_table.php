<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('lesson_plan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['document', 'video', 'audio', 'image', 'link', 'youtube', 'presentation', 'other'])->default('document');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->nullable(); // in bytes
            $table->string('external_url')->nullable(); // For YouTube, Google Drive, etc.
            $table->string('thumbnail')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('downloads_count')->default(0);
            $table->boolean('is_downloadable')->default(true);
            $table->boolean('is_public')->default(false); // Visible to all or specific classes
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['teacher_id', 'subject_id']);
            $table->index(['class_id', 'type']);
        });

        // Track which students have accessed materials
        Schema::create('material_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_material_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->boolean('viewed')->default(false);
            $table->boolean('downloaded')->default(false);
            $table->timestamp('first_accessed_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->integer('access_count')->default(0);
            $table->timestamps();

            $table->unique(['learning_material_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_accesses');
        Schema::dropIfExists('learning_materials');
    }
};
