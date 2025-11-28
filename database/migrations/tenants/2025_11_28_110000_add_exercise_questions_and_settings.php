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
        // Add new fields to exercises table (only if they don't exist)
        Schema::table('exercises', function (Blueprint $table) {
            if (!Schema::hasColumn('exercises', 'auto_grade')) {
                $table->boolean('auto_grade')->default(false)->after('submission_type');
            }
            if (!Schema::hasColumn('exercises', 'show_answers_after_submit')) {
                $table->boolean('show_answers_after_submit')->default(false)->after('auto_grade');
            }
            if (!Schema::hasColumn('exercises', 'allow_file_upload')) {
                $table->boolean('allow_file_upload')->default(true)->after('show_answers_after_submit');
            }
            if (!Schema::hasColumn('exercises', 'allow_text_response')) {
                $table->boolean('allow_text_response')->default(true)->after('allow_file_upload');
            }
            if (!Schema::hasColumn('exercises', 'allowed_file_types')) {
                $table->json('allowed_file_types')->nullable()->after('allow_text_response');
            }
            if (!Schema::hasColumn('exercises', 'max_file_size_mb')) {
                $table->integer('max_file_size_mb')->default(10)->after('allowed_file_types');
            }
        });

        // Create exercise_questions table for structured questions
        if (!Schema::hasTable('exercise_questions')) {
            Schema::create('exercise_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
                $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'essay', 'fill_blank', 'matching']);
                $table->longText('question');
                $table->json('options')->nullable(); // For multiple choice, matching
                $table->json('correct_answer')->nullable(); // For auto-grading
                $table->decimal('marks', 8, 2)->default(1);
                $table->integer('order')->default(0);
                $table->text('explanation')->nullable(); // Shown after grading
                $table->boolean('is_required')->default(true);
                $table->timestamps();

                $table->index(['exercise_id', 'order']);
            });
        }

        // Create exercise_attachments table for file uploads by teacher
        if (!Schema::hasTable('exercise_attachments')) {
            Schema::create('exercise_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
                $table->string('filename');
                $table->string('original_name');
                $table->string('mime_type');
                $table->bigInteger('file_size');
                $table->string('path');
                $table->timestamps();
            });
        }

        // Add question_answers to exercise_submissions for structured responses
        Schema::table('exercise_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('exercise_submissions', 'question_answers')) {
                $table->json('question_answers')->nullable()->after('content');
            }
            if (!Schema::hasColumn('exercise_submissions', 'auto_score')) {
                $table->decimal('auto_score', 8, 2)->nullable()->after('score');
            }
            if (!Schema::hasColumn('exercise_submissions', 'manual_score')) {
                $table->decimal('manual_score', 8, 2)->nullable()->after('auto_score');
            }
            if (!Schema::hasColumn('exercise_submissions', 'is_graded')) {
                $table->boolean('is_graded')->default(false)->after('manual_score');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exercise_submissions', function (Blueprint $table) {
            $table->dropColumn(['question_answers', 'auto_score', 'manual_score', 'is_graded']);
        });

        Schema::dropIfExists('exercise_attachments');
        Schema::dropIfExists('exercise_questions');

        Schema::table('exercises', function (Blueprint $table) {
            $table->dropColumn([
                'auto_grade',
                'show_answers_after_submit',
                'allow_file_upload',
                'allow_text_response',
                'allowed_file_types',
                'max_file_size_mb'
            ]);
        });
    }
};
