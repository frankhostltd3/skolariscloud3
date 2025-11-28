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
        Schema::table('online_exam_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('online_exam_attempts', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('online_exam_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('online_exam_attempts', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
