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
        Schema::table('online_exam_sections', function (Blueprint $table) {
            if (!Schema::hasColumn('online_exam_sections', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('online_exam_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('online_exam_questions', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('online_exam_answers', function (Blueprint $table) {
            if (!Schema::hasColumn('online_exam_answers', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('online_exam_sections', function (Blueprint $table) {
            if (Schema::hasColumn('online_exam_sections', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('online_exam_questions', function (Blueprint $table) {
            if (Schema::hasColumn('online_exam_questions', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('online_exam_answers', function (Blueprint $table) {
            if (Schema::hasColumn('online_exam_answers', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
