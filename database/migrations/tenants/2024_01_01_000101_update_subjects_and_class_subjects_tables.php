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
        // Update subjects table
        Schema::table('subjects', function (Blueprint $table) {
            // Drop old columns if they exist
            if (Schema::hasColumn('subjects', 'category')) {
                $table->dropColumn('category');
            }
            if (Schema::hasColumn('subjects', 'is_compulsory')) {
                $table->dropColumn('is_compulsory');
            }

            // Add new columns
            if (!Schema::hasColumn('subjects', 'type')) {
                $table->enum('type', ['core', 'elective', 'optional'])->default('core')->after('description');
            }
            if (!Schema::hasColumn('subjects', 'credit_hours')) {
                $table->integer('credit_hours')->nullable()->after('type');
            }
            if (!Schema::hasColumn('subjects', 'max_marks')) {
                $table->integer('max_marks')->default(100)->after('pass_mark');
            }
            if (!Schema::hasColumn('subjects', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }

            // Update pass_mark to have default value
            $table->integer('pass_mark')->default(40)->change();
        });

        // Rename class_subjects to class_subject if it exists
        if (Schema::hasTable('class_subjects') && !Schema::hasTable('class_subject')) {
            Schema::rename('class_subjects', 'class_subject');
        }

        // Update class_subject table
        if (Schema::hasTable('class_subject')) {
            Schema::table('class_subject', function (Blueprint $table) {
                // Add is_compulsory column if it doesn't exist
                if (!Schema::hasColumn('class_subject', 'is_compulsory')) {
                    $table->boolean('is_compulsory')->default(true)->after('teacher_id');
                }

                // Drop periods_per_week and is_active if they exist (moved to timetable management)
                if (Schema::hasColumn('class_subject', 'periods_per_week')) {
                    $table->dropColumn('periods_per_week');
                }
                if (Schema::hasColumn('class_subject', 'is_active')) {
                    $table->dropColumn('is_active');
                }
            });
        }

        // Add unique constraint on school_id + code
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'unique_school_code')) {
                $table->unique(['school_id', 'code'], 'unique_school_code');
            }
        });

        // Add composite indexes for better performance
        Schema::table('subjects', function (Blueprint $table) {
            $table->index(['school_id', 'is_active']);
            $table->index(['school_id', 'education_level_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse subjects table changes
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('subjects', 'credit_hours')) {
                $table->dropColumn('credit_hours');
            }
            if (Schema::hasColumn('subjects', 'max_marks')) {
                $table->dropColumn('max_marks');
            }
            if (Schema::hasColumn('subjects', 'sort_order')) {
                $table->dropColumn('sort_order');
            }

            $table->string('category')->nullable();
            $table->boolean('is_compulsory')->default(false);
        });

        // Reverse class_subject table changes
        if (Schema::hasTable('class_subject')) {
            Schema::table('class_subject', function (Blueprint $table) {
                if (Schema::hasColumn('class_subject', 'is_compulsory')) {
                    $table->dropColumn('is_compulsory');
                }

                $table->integer('periods_per_week')->nullable();
                $table->boolean('is_active')->default(true);
            });

            Schema::rename('class_subject', 'class_subjects');
        }

        // Drop indexes and unique constraints
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropUnique('unique_school_code');
            $table->dropIndex(['school_id', 'is_active']);
            $table->dropIndex(['school_id', 'education_level_id']);
        });
    }
};
