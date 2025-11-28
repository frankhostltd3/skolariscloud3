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
        Schema::table('quizzes', function (Blueprint $table) {
            if (!Schema::hasColumn('quizzes', 'available_from')) {
                $table->dateTime('available_from')->nullable()->after('instructions');
            }
            if (!Schema::hasColumn('quizzes', 'available_until')) {
                $table->dateTime('available_until')->nullable()->after('available_from');
            }
            if (!Schema::hasColumn('quizzes', 'duration_minutes')) {
                $table->integer('duration_minutes')->nullable()->after('available_until');
            }
            if (!Schema::hasColumn('quizzes', 'total_marks')) {
                $table->integer('total_marks')->default(0)->after('duration_minutes');
            }
            if (!Schema::hasColumn('quizzes', 'pass_marks')) {
                $table->integer('pass_marks')->nullable()->after('total_marks');
            }
            if (!Schema::hasColumn('quizzes', 'max_attempts')) {
                $table->integer('max_attempts')->default(1)->after('pass_marks');
            }
            if (!Schema::hasColumn('quizzes', 'shuffle_questions')) {
                $table->boolean('shuffle_questions')->default(false)->after('max_attempts');
            }
            if (!Schema::hasColumn('quizzes', 'shuffle_answers')) {
                $table->boolean('shuffle_answers')->default(false)->after('shuffle_questions');
            }
            if (!Schema::hasColumn('quizzes', 'show_results_immediately')) {
                $table->boolean('show_results_immediately')->default(true)->after('shuffle_answers');
            }
            if (!Schema::hasColumn('quizzes', 'show_correct_answers')) {
                $table->boolean('show_correct_answers')->default(true)->after('show_results_immediately');
            }
            if (!Schema::hasColumn('quizzes', 'allow_review')) {
                $table->boolean('allow_review')->default(true)->after('show_correct_answers');
            }
            if (!Schema::hasColumn('quizzes', 'status')) {
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->after('allow_review');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $columns = [
                'available_from',
                'available_until',
                'duration_minutes',
                'total_marks',
                'pass_marks',
                'max_attempts',
                'shuffle_questions',
                'shuffle_answers',
                'show_results_immediately',
                'show_correct_answers',
                'allow_review',
                'status'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('quizzes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
