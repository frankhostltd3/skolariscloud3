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
            // Add new columns if they don't exist (keep existing school_id and code)
            if (!Schema::hasColumn('expense_categories', 'color')) {
                $table->string('color', 7)->default('#6c757d')->after('description');
            }

            if (!Schema::hasColumn('expense_categories', 'icon')) {
                $table->string('icon')->default('bi-receipt')->after('color');
            }

            if (!Schema::hasColumn('expense_categories', 'budget_limit')) {
                $table->decimal('budget_limit', 15, 2)->nullable()->after('is_active');
            }

            if (!Schema::hasColumn('expense_categories', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('budget_limit');
            }
        });

        // Add foreign key if parent_id column was added
        if (Schema::hasColumn('expense_categories', 'parent_id')) {
            try {
                Schema::table('expense_categories', function (Blueprint $table) {
                    $table->foreign('parent_id')
                        ->references('id')
                        ->on('expense_categories')
                        ->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist, ignore error
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            // Remove new columns only
            if (Schema::hasColumn('expense_categories', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }

            if (Schema::hasColumn('expense_categories', 'budget_limit')) {
                $table->dropColumn('budget_limit');
            }

            if (Schema::hasColumn('expense_categories', 'icon')) {
                $table->dropColumn('icon');
            }

            if (Schema::hasColumn('expense_categories', 'color')) {
                $table->dropColumn('color');
            }
        });
    }
};
