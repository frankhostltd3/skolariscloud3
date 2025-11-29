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
        Schema::table('parent_student', function (Blueprint $table) {
            // Fix can_pickup
            if (Schema::hasColumn('parent_student', 'can_pickup_student')) {
                $table->renameColumn('can_pickup_student', 'can_pickup');
            } elseif (!Schema::hasColumn('parent_student', 'can_pickup')) {
                $table->boolean('can_pickup')->default(true);
            }

            // Fix is_primary
            if (Schema::hasColumn('parent_student', 'is_primary_contact')) {
                $table->renameColumn('is_primary_contact', 'is_primary');
            } elseif (!Schema::hasColumn('parent_student', 'is_primary')) {
                $table->boolean('is_primary')->default(false);
            }

            // Fix financial_responsibility
            if (!Schema::hasColumn('parent_student', 'financial_responsibility')) {
                $table->boolean('financial_responsibility')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parent_student', function (Blueprint $table) {
            if (Schema::hasColumn('parent_student', 'can_pickup')) {
                $table->renameColumn('can_pickup', 'can_pickup_student');
            }
            if (Schema::hasColumn('parent_student', 'is_primary')) {
                $table->renameColumn('is_primary', 'is_primary_contact');
            }
            if (Schema::hasColumn('parent_student', 'financial_responsibility')) {
                $table->dropColumn('financial_responsibility');
            }
        });
    }
};
