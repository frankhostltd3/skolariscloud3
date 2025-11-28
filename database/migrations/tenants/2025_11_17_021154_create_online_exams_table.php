<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Note: This extends the existing exams system with online capabilities
        if (!Schema::hasTable('online_exams')) {
            Schema::create('online_exams', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('instructions')->nullable();
                $table->dateTime('starts_at');
                $table->dateTime('ends_at');
                $table->integer('duration_minutes');
                $table->integer('total_marks');
                $table->integer('pass_marks')->nullable();
                $table->boolean('shuffle_questions')->default(false);
                $table->boolean('shuffle_options')->default(false);
                $table->boolean('allow_backtrack')->default(true); // Can go back to previous questions
                $table->boolean('show_results_immediately')->default(false);
                $table->boolean('proctored')->default(false); // Requires webcam/screen monitoring
                $table->integer('max_tab_switches')->default(5); // Security: limit tab switching
                $table->boolean('disable_copy_paste')->default(true);
                $table->enum('auto_submit_on', ['time_up', 'manual', 'both'])->default('both');
                $table->enum('grading_method', ['auto', 'manual', 'mixed'])->default('auto');
                $table->enum('status', ['draft', 'scheduled', 'active', 'completed', 'archived'])->default('draft');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['teacher_id', 'starts_at']);
                $table->index(['class_id', 'status']);
            });
        }

        if (!Schema::hasTable('online_exam_sections')) {
            Schema::create('online_exam_sections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('online_exam_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->integer('order')->default(0);
                $table->integer('time_limit_minutes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('online_exam_questions')) {
            Schema::create('online_exam_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('online_exam_id')->constrained()->onDelete('cascade');
                $table->foreignId('section_id')->nullable()->constrained('online_exam_sections')->onDelete('set null');
                $table->enum('type', ['multiple_choice', 'multiple_answer', 'true_false', 'short_answer', 'essay', 'fill_blank', 'matching'])->default('multiple_choice');
                $table->text('question');
                $table->text('question_image')->nullable();
                $table->text('explanation')->nullable();
                $table->json('options')->nullable();
                $table->text('correct_answer')->nullable();
                $table->integer('marks');
                $table->integer('negative_marks')->default(0);
                $table->integer('order')->default(0);
                $table->boolean('is_required')->default(true);
                $table->timestamps();

                $table->index(['online_exam_id', 'order']);
            });
        }

        if (!Schema::hasTable('online_exam_attempts')) {
            Schema::create('online_exam_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('online_exam_id')->constrained()->onDelete('cascade');
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->dateTime('started_at');
                $table->dateTime('submitted_at')->nullable();
                $table->dateTime('auto_submitted_at')->nullable();
                $table->integer('time_taken_minutes')->nullable();
                $table->decimal('score', 6, 2)->nullable();
                $table->decimal('percentage', 5, 2)->nullable();
                $table->enum('status', ['in_progress', 'submitted', 'graded', 'flagged'])->default('in_progress');
                $table->integer('tab_switches_count')->default(0);
                $table->json('violation_logs')->nullable(); // Security violations
                $table->text('proctor_notes')->nullable();
                $table->boolean('is_verified')->default(true);
                $table->timestamps();

                $table->index(['online_exam_id', 'student_id']);
            });
        }

        if (!Schema::hasTable('online_exam_answers')) {
            Schema::create('online_exam_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('online_exam_attempt_id')->constrained()->onDelete('cascade');
                $table->foreignId('online_exam_question_id')->constrained()->onDelete('cascade');
                $table->text('answer')->nullable();
                $table->json('selected_options')->nullable();
                $table->boolean('is_correct')->nullable();
                $table->decimal('marks_awarded', 5, 2)->default(0);
                $table->text('teacher_feedback')->nullable();
                $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
                $table->dateTime('graded_at')->nullable();
                $table->timestamp('answered_at')->nullable();
                $table->timestamps();

                $table->unique(['online_exam_attempt_id', 'online_exam_question_id'], 'attempt_question_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('online_exam_answers');
        Schema::dropIfExists('online_exam_attempts');
        Schema::dropIfExists('online_exam_questions');
        Schema::dropIfExists('online_exam_sections');
        Schema::dropIfExists('online_exams');
    }
};
