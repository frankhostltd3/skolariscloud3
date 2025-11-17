<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'max_capacity')) {
                $table->unsignedSmallInteger('max_capacity')->nullable()->after('name');
            }
            if (!Schema::hasColumn('classes', 'class_teacher_id')) {
                $table->foreignId('class_teacher_id')->nullable()->after('max_capacity')->constrained('teachers')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'class_teacher_id')) {
                $table->dropConstrainedForeignId('class_teacher_id');
            }
            if (Schema::hasColumn('classes', 'max_capacity')) {
                $table->dropColumn('max_capacity');
            }
        });
    }
};
