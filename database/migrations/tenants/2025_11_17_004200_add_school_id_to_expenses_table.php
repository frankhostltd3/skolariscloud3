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
        Schema::table('expenses', function (Blueprint $table) {
            // Add school_id if it doesn't exist
            if (!Schema::hasColumn('expenses', 'school_id')) {
                $table->foreignId('school_id')->after('id')->nullable()->constrained()->onDelete('cascade');
            }
        });

        // Add index
        try {
            Schema::table('expenses', function (Blueprint $table) {
                if (Schema::hasColumn('expenses', 'school_id')) {
                    $table->index('school_id');
                }
            });
        } catch (\Exception $e) {
            // Index might already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'school_id')) {
                $table->dropForeign(['school_id']);
                $table->dropIndex(['school_id']);
                $table->dropColumn('school_id');
            }
        });
    }
};
