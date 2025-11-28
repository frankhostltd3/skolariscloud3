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
        Schema::table('grades', function (Blueprint $table) {
            // Add new columns
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained('terms')->nullOnDelete();
            $table->string('assessment_type')->nullable();
            $table->string('assessment_name')->nullable();
            $table->decimal('marks_obtained', 5, 2)->nullable();
            $table->decimal('total_marks', 5, 2)->nullable();
            $table->string('grade_letter')->nullable();
            $table->decimal('grade_point', 3, 2)->nullable();
            $table->date('assessment_date')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_published')->default(false);
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();

            // Drop old columns if they exist
            if (Schema::hasColumn('grades', 'term')) {
                $table->dropColumn('term');
            }
            if (Schema::hasColumn('grades', 'score')) {
                $table->dropColumn('score');
            }
            if (Schema::hasColumn('grades', 'awarded_on')) {
                $table->dropColumn('awarded_on');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            // Add back old columns
            $table->string('term')->nullable();
            $table->string('score')->nullable();
            $table->date('awarded_on')->nullable();

            // Drop new columns
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
            $table->dropForeign(['semester_id']);
            $table->dropColumn('semester_id');
            $table->dropColumn('assessment_type');
            $table->dropColumn('assessment_name');
            $table->dropColumn('marks_obtained');
            $table->dropColumn('total_marks');
            $table->dropColumn('grade_letter');
            $table->dropColumn('grade_point');
            $table->dropColumn('assessment_date');
            $table->dropColumn('remarks');
            $table->dropColumn('is_published');
            $table->dropForeign(['entered_by']);
            $table->dropColumn('entered_by');
            $table->dropColumn('published_at');
        });
    }
};
