<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (! Schema::hasColumn('employees', 'is_teacher')) {
                    $table->boolean('is_teacher')->default(false)->after('employment_status');
                }

                if (! Schema::hasColumn('employees', 'teacher_id')) {
                    $table->foreignId('teacher_id')->nullable()->after('is_teacher')->constrained('teachers')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('teachers')) {
            Schema::table('teachers', function (Blueprint $table) {
                $positionCandidates = ['employee_id', 'user_id', 'school_id', 'id'];
                $employeeNumberPosition = collect($positionCandidates)
                    ->first(fn ($column) => Schema::hasColumn('teachers', $column)) ?? 'id';

                if (! Schema::hasColumn('teachers', 'employee_number')) {
                    $table->string('employee_number')->nullable()->after($employeeNumberPosition);
                }

                if (! Schema::hasColumn('teachers', 'employee_record_id')) {
                    $table->foreignId('employee_record_id')->nullable()->after('employee_number')->constrained('employees')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (Schema::hasColumn('employees', 'teacher_id')) {
                    $table->dropForeign(['teacher_id']);
                }

                $columns = collect(['is_teacher', 'teacher_id'])
                    ->filter(fn ($column) => Schema::hasColumn('employees', $column))
                    ->all();

                if (! empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }

        if (Schema::hasTable('teachers')) {
            Schema::table('teachers', function (Blueprint $table) {
                if (Schema::hasColumn('teachers', 'employee_record_id')) {
                    $table->dropForeign(['employee_record_id']);
                }

                $columns = collect(['employee_number', 'employee_record_id'])
                    ->filter(fn ($column) => Schema::hasColumn('teachers', $column))
                    ->all();

                if (! empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
