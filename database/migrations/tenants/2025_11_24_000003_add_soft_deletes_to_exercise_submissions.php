<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('exercise_submissions')) {
            Schema::table('exercise_submissions', function (Blueprint $table) {
                if (!Schema::hasColumn('exercise_submissions', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('exercise_submissions')) {
            Schema::table('exercise_submissions', function (Blueprint $table) {
                if (Schema::hasColumn('exercise_submissions', 'deleted_at')) {
                    $table->dropSoftDeletes();
                }
            });
        }
    }
};
