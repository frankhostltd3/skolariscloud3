<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('classes')) return; // safety
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'code')) {
                $table->string('code')->nullable()->after('name');
            }
            if (!Schema::hasColumn('classes', 'grade_level')) { // avoid duplicate if earlier migration missed in some tenants
                $table->string('grade_level')->nullable()->after('code');
            }
            if (!Schema::hasColumn('classes', 'section')) {
                $table->string('section', 50)->nullable()->after('grade_level');
            }
            if (!Schema::hasColumn('classes', 'stream')) {
                $table->string('stream', 50)->nullable()->after('section');
            }
            if (!Schema::hasColumn('classes', 'capacity')) {
                $table->unsignedSmallInteger('capacity')->nullable()->after('stream');
            }
            if (!Schema::hasColumn('classes', 'current_enrollment')) {
                $table->unsignedSmallInteger('current_enrollment')->default(0)->after('capacity');
            }
            if (!Schema::hasColumn('classes', 'class_teacher_id')) { // if earlier migration didn't add it
                $table->foreignId('class_teacher_id')->nullable()->after('current_enrollment')->constrained('teachers')->nullOnDelete();
            }
            if (!Schema::hasColumn('classes', 'room_number')) {
                $table->string('room_number', 50)->nullable()->after('class_teacher_id');
            }
            if (!Schema::hasColumn('classes', 'description')) {
                $table->text('description')->nullable()->after('room_number');
            }
            if (!Schema::hasColumn('classes', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
            if (!Schema::hasColumn('classes', 'academic_year_id')) {
                $table->foreignId('academic_year_id')->nullable()->after('is_active')->constrained('academic_years')->nullOnDelete();
            }
            if (!Schema::hasColumn('classes', 'start_time')) {
                $table->time('start_time')->nullable()->after('academic_year_id');
            }
            if (!Schema::hasColumn('classes', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
        });

        // Backfill capacity from legacy max_capacity if present
        if (Schema::hasColumn('classes', 'max_capacity') && Schema::hasColumn('classes', 'capacity')) {
            DB::table('classes')->whereNull('capacity')->update(['capacity' => DB::raw('max_capacity')]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('classes')) return;
        Schema::table('classes', function (Blueprint $table) {
            $cols = [ 'code','section','stream','capacity','current_enrollment','room_number','description','is_active','academic_year_id','start_time','end_time' ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('classes', $col)) {
                    if (in_array($col, ['class_teacher_id','academic_year_id'])) continue; // keep FKs / or skip drop for safety
                }
            }
            // We intentionally do not drop columns in down to avoid data loss in production rollbacks.
        });
    }
};

