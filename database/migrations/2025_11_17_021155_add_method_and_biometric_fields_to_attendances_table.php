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
        if (! Schema::hasTable('attendances')) {
            return;
        }

        Schema::table('attendances', function (Blueprint $table) {
            // Rename 'date' to 'attendance_date' for clarity
            $table->renameColumn('date', 'attendance_date');

            // Add method field (manual, fingerprint, iris, barcode)
            $table->enum('method', ['manual', 'fingerprint', 'iris', 'barcode'])
                  ->default('manual')
                  ->after('marked_by');

            // Add biometric verification flag
            $table->boolean('biometric_verified')
                  ->default(false)
                  ->after('method');

            // Remove marked_at as created_at can serve this purpose
            $table->dropColumn('marked_at');
        });

        // Update the unique constraint to use new column name
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'class_id', 'date']);
            $table->unique(['student_id', 'class_id', 'attendance_date']);

            // Update indexes
            $table->dropIndex(['class_id', 'date']);
            $table->dropIndex(['student_id', 'date']);
            $table->index(['class_id', 'attendance_date']);
            $table->index(['student_id', 'attendance_date']);
            $table->index('method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('attendances')) {
            return;
        }

        Schema::table('attendances', function (Blueprint $table) {
            // Drop new indexes
            $table->dropIndex(['class_id', 'attendance_date']);
            $table->dropIndex(['student_id', 'attendance_date']);
            $table->dropIndex(['method']);

            // Drop unique constraint
            $table->dropUnique(['student_id', 'class_id', 'attendance_date']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            // Rename back to 'date'
            $table->renameColumn('attendance_date', 'date');

            // Drop new columns
            $table->dropColumn(['method', 'biometric_verified']);

            // Restore marked_at
            $table->timestamp('marked_at')->nullable()->after('marked_by');

            // Restore old indexes and constraints
            $table->unique(['student_id', 'class_id', 'date']);
            $table->index(['class_id', 'date']);
            $table->index(['student_id', 'date']);
        });
    }
};
