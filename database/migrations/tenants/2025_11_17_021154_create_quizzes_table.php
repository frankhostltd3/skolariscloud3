<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('quizzes')) {
            Schema::create('quizzes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('instructions')->nullable();
                $table->dateTime('available_from')->nullable();
                $table->dateTime('available_until')->nullable();
                $table->integer('duration_minutes')->nullable(); // Time limit
                $table->integer('total_marks')->default(0);
                $table->integer('pass_marks')->nullable();
                $table->integer('max_attempts')->default(1);
                $table->boolean('shuffle_questions')->default(false);
                $table->boolean('shuffle_answers')->default(false);
                $table->boolean('show_results_immediately')->default(true);
                $table->boolean('show_correct_answers')->default(true);
                $table->boolean('allow_review')->default(true);
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['teacher_id', 'available_from']);
                $table->index(['class_id', 'status']);
            });
        }

        if (!Schema::hasTable('quiz_questions')) {
            Schema::create('quiz_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
                $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'essay'])->default('multiple_choice');
                $table->text('question');
                $table->text('explanation')->nullable();
                $table->integer('marks')->default(1);
                $table->integer('order')->default(0);
                $table->json('options')->nullable(); // For multiple choice
                $table->text('correct_answer')->nullable();
                $table->boolean('is_required')->default(true);
                $table->timestamps();

                $table->index(['quiz_id', 'order']);
            });
        }

        if (!Schema::hasTable('quiz_attempts')) {
            Schema::create('quiz_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->integer('attempt_number')->default(1);
                $table->dateTime('started_at');
                $table->dateTime('submitted_at')->nullable();
                $table->decimal('score', 5, 2)->nullable();
                $table->decimal('percentage', 5, 2)->nullable();
                $table->enum('status', ['in_progress', 'submitted', 'graded'])->default('in_progress');
                $table->json('answers')->nullable();
                $table->integer('time_taken_minutes')->nullable();
                $table->timestamps();

                $table->index(['quiz_id', 'student_id']);
            });
        }

        if (!Schema::hasTable('quiz_answers')) {
            Schema::create('quiz_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quiz_attempt_id')->constrained()->onDelete('cascade');
                $table->foreignId('quiz_question_id')->constrained()->onDelete('cascade');
                $table->text('answer')->nullable();
                $table->boolean('is_correct')->nullable();
                $table->decimal('marks_awarded', 5, 2)->default(0);
                $table->text('feedback')->nullable();
                $table->timestamps();

                $table->unique(['quiz_attempt_id', 'quiz_question_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
