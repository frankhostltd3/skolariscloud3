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
        if (! Schema::hasTable('grades')) {
            return;
        }

        Schema::table('grades', function (Blueprint $table) {
            // Add missing columns for comprehensive grading system
            if (!Schema::hasColumn('grades', 'class_id')) {
                $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete()->after('subject_id');
            }
            if (!Schema::hasColumn('grades', 'semester_id')) {
                $table->foreignId('semester_id')->nullable()->constrained('terms')->nullOnDelete()->after('class_id');
            }
            if (!Schema::hasColumn('grades', 'assessment_type')) {
                $table->string('assessment_type')->default('exam')->after('semester_id');
            }
            if (!Schema::hasColumn('grades', 'assessment_name')) {
                $table->string('assessment_name')->nullable()->after('assessment_type');
            }
            if (!Schema::hasColumn('grades', 'marks_obtained')) {
                $table->decimal('marks_obtained', 8, 2)->nullable()->after('assessment_name');
            }
            if (!Schema::hasColumn('grades', 'total_marks')) {
                $table->decimal('total_marks', 8, 2)->nullable()->after('marks_obtained');
            }
            if (!Schema::hasColumn('grades', 'grade_letter')) {
                $table->string('grade_letter', 5)->nullable()->after('total_marks');
            }
            if (!Schema::hasColumn('grades', 'grade_point')) {
                $table->decimal('grade_point', 3, 2)->nullable()->after('grade_letter');
            }
            if (!Schema::hasColumn('grades', 'assessment_date')) {
                $table->date('assessment_date')->nullable()->after('grade_point');
            }
            if (!Schema::hasColumn('grades', 'remarks')) {
                $table->text('remarks')->nullable()->after('assessment_date');
            }
            if (!Schema::hasColumn('grades', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('remarks');
            }
            if (!Schema::hasColumn('grades', 'entered_by')) {
                $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete()->after('is_published');
            }
            if (!Schema::hasColumn('grades', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('entered_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('grades')) {
            return;
        }

        Schema::table('grades', function (Blueprint $table) {
            // Drop added columns in reverse order
            if (Schema::hasColumn('grades', 'published_at')) {
                $table->dropColumn('published_at');
            }
            if (Schema::hasColumn('grades', 'entered_by')) {
                $table->dropForeign(['entered_by']);
                $table->dropColumn('entered_by');
            }
            if (Schema::hasColumn('grades', 'is_published')) {
                $table->dropColumn('is_published');
            }
            if (Schema::hasColumn('grades', 'remarks')) {
                $table->dropColumn('remarks');
            }
            if (Schema::hasColumn('grades', 'assessment_date')) {
                $table->dropColumn('assessment_date');
            }
            if (Schema::hasColumn('grades', 'grade_point')) {
                $table->dropColumn('grade_point');
            }
            if (Schema::hasColumn('grades', 'grade_letter')) {
                $table->dropColumn('grade_letter');
            }
            if (Schema::hasColumn('grades', 'total_marks')) {
                $table->dropColumn('total_marks');
            }
            if (Schema::hasColumn('grades', 'marks_obtained')) {
                $table->dropColumn('marks_obtained');
            }
            if (Schema::hasColumn('grades', 'assessment_name')) {
                $table->dropColumn('assessment_name');
            }
            if (Schema::hasColumn('grades', 'assessment_type')) {
                $table->dropColumn('assessment_type');
            }
            if (Schema::hasColumn('grades', 'semester_id')) {
                $table->dropForeign(['semester_id']);
                $table->dropColumn('semester_id');
            }
            if (Schema::hasColumn('grades', 'class_id')) {
                $table->dropForeign(['class_id']);
                $table->dropColumn('class_id');
            }
        });
    }
};
