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
            if (!Schema::hasColumn('expense_categories', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('school_id')->constrained('expense_categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('expense_categories', 'color')) {
                $table->string('color', 7)->default('#6c757d')->after('description');
            }
            if (!Schema::hasColumn('expense_categories', 'icon')) {
                $table->string('icon')->default('bi-receipt')->after('color');
            }
            if (!Schema::hasColumn('expense_categories', 'budget_limit')) {
                $table->decimal('budget_limit', 15, 2)->nullable()->after('icon');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'color', 'icon', 'budget_limit']);
        });
    }
};
