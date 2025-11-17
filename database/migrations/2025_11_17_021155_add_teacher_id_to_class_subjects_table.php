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
        Schema::table('class_subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('class_subjects', 'teacher_id')) {
                $table->foreignId('teacher_id')->nullable()->after('subject_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('class_subjects', 'periods_per_week')) {
                $table->integer('periods_per_week')->nullable()->after('teacher_id');
            }
            if (!Schema::hasColumn('class_subjects', 'start_time')) {
                $table->time('start_time')->nullable()->after('periods_per_week');
            }
            if (!Schema::hasColumn('class_subjects', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
            if (!Schema::hasColumn('class_subjects', 'schedule_days')) {
                $table->json('schedule_days')->nullable()->after('end_time');
            }
            if (!Schema::hasColumn('class_subjects', 'room_number')) {
                $table->string('room_number', 50)->nullable()->after('schedule_days');
            }
            if (!Schema::hasColumn('class_subjects', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('room_number');
            }
            if (!Schema::hasColumn('class_subjects', 'notes')) {
                $table->text('notes')->nullable()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_subjects', function (Blueprint $table) {
            $columns = ['notes', 'is_active', 'room_number', 'schedule_days', 'end_time', 'start_time', 'periods_per_week', 'teacher_id'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('class_subjects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
