<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add school_id to exercises table if not exists
        if (Schema::hasTable('exercises') && !Schema::hasColumn('exercises', 'school_id')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->foreignId('school_id')->after('id')->nullable()->constrained('schools')->onDelete('cascade');
            });
        }

        // Add grade column to exercise_submissions if not exists
        if (Schema::hasTable('exercise_submissions') && !Schema::hasColumn('exercise_submissions', 'grade')) {
            Schema::table('exercise_submissions', function (Blueprint $table) {
                $table->decimal('grade', 5, 2)->nullable()->after('score');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('exercises', 'school_id')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            });
        }

        if (Schema::hasColumn('exercise_submissions', 'grade')) {
            Schema::table('exercise_submissions', function (Blueprint $table) {
                $table->dropColumn('grade');
            });
        }
    }
};
