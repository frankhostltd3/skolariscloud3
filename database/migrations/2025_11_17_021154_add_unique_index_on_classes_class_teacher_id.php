<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('classes')) {
            return;
        }

        Schema::table('classes', function (Blueprint $table) {
            // Add a unique index so a teacher can be assigned to at most one class (multiple NULLs allowed)
            if (! Schema::hasColumn('classes', 'class_teacher_id')) {
                return;
            }

            $table->unique('class_teacher_id', 'classes_class_teacher_id_unique');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('classes')) {
            return;
        }

        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'class_teacher_id')) {
                $table->dropUnique('classes_class_teacher_id_unique');
            }
        });
    }
};
