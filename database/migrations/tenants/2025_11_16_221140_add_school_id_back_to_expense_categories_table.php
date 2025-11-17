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
        Schema::table('expense_categories', function (Blueprint $table) {
            // Add school_id column back if it doesn't exist
            if (!Schema::hasColumn('expense_categories', 'school_id')) {
                $table->foreignId('school_id')->after('id')->constrained('schools')->onDelete('cascade');
                $table->index('school_id');
            }

            // Also add code column back if it doesn't exist
            if (!Schema::hasColumn('expense_categories', 'code')) {
                $table->string('code', 50)->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            // Don't remove school_id and code on rollback - they're essential
        });
    }
};
