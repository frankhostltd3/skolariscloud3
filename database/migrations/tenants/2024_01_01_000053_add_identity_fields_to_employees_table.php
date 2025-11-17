<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'national_id')) {
                $table->string('national_id')->nullable()->unique()->after('employee_number');
            }
            if (!Schema::hasColumn('employees', 'gender')) {
                $table->string('gender', 16)->nullable()->after('national_id'); // enforce via validation (male,female,other)
            }
            if (!Schema::hasColumn('employees', 'photo_path')) {
                $table->string('photo_path')->nullable()->after('employment_status');
            }
            if (!Schema::hasColumn('employees', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('photo_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
            if (Schema::hasColumn('employees', 'photo_path')) {
                $table->dropColumn('photo_path');
            }
            if (Schema::hasColumn('employees', 'gender')) {
                $table->dropColumn('gender');
            }
            if (Schema::hasColumn('employees', 'national_id')) {
                $table->dropColumn('national_id');
            }
        });
    }
};
