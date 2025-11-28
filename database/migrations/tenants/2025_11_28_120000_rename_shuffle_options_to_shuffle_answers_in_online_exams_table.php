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
        Schema::table('online_exams', function (Blueprint $table) {
            if (Schema::hasColumn('online_exams', 'shuffle_options') && !Schema::hasColumn('online_exams', 'shuffle_answers')) {
                $table->renameColumn('shuffle_options', 'shuffle_answers');
            } elseif (!Schema::hasColumn('online_exams', 'shuffle_answers')) {
                $table->boolean('shuffle_answers')->default(false)->after('shuffle_questions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('online_exams', function (Blueprint $table) {
            if (Schema::hasColumn('online_exams', 'shuffle_answers')) {
                $table->renameColumn('shuffle_answers', 'shuffle_options');
            }
        });
    }
};
