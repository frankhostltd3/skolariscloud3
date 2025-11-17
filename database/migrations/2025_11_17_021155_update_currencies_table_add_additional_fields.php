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
        Schema::table('currencies', function (Blueprint $table) {
            // Rename decimals to decimal_places for consistency
            $table->renameColumn('decimals', 'decimal_places');

            // Rename enabled to is_active for consistency
            $table->renameColumn('enabled', 'is_active');

            // Add new fields
            $table->enum('symbol_position', ['before', 'after'])->default('before')->after('symbol');
            $table->string('thousands_separator', 5)->default(',')->after('decimal_places');
            $table->string('decimal_separator', 5)->default('.')->after('thousands_separator');
            $table->boolean('is_default')->default(false)->after('is_active');

            // Update index
            $table->dropIndex(['code', 'enabled']);
            $table->index(['code', 'is_active', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            // Reverse the changes
            $table->dropIndex(['code', 'is_active', 'is_default']);
            $table->index(['code', 'enabled']);

            $table->dropColumn(['symbol_position', 'thousands_separator', 'decimal_separator', 'is_default']);

            // Rename back
            $table->renameColumn('decimal_places', 'decimals');
            $table->renameColumn('is_active', 'enabled');
        });
    }
};
