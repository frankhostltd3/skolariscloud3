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
        Schema::table('timetable_entries', function (Blueprint $table) {
            // Drop the existing foreign key to teachers table
            $table->dropForeign(['teacher_id']);

            // Add new foreign key to users table
            $table->foreign('teacher_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);

            $table->foreign('teacher_id')
                  ->references('id')
                  ->on('teachers')
                  ->nullOnDelete();
        });
    }
};
