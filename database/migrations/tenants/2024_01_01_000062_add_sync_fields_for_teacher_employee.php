<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add sync fields to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('is_teacher')->default(false)->after('employment_status');
            $table->foreignId('teacher_id')->nullable()->after('is_teacher')->constrained('teachers')->nullOnDelete();
        });

        // Add sync fields to teachers table
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('employee_number')->nullable()->after('employee_id');
            $table->foreignId('employee_record_id')->nullable()->after('employee_number')->constrained('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn(['is_teacher', 'teacher_id']);
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['employee_record_id']);
            $table->dropColumn(['employee_number', 'employee_record_id']);
        });
    }
};
