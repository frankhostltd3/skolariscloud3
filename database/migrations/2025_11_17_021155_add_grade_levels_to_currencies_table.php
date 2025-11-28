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
        $schema = Schema::connection('tenant');

        if (! $schema->hasTable('currencies')) {
            return;
        }

        $schema->table('currencies', function (Blueprint $table) use ($schema) {
            if ($schema->hasColumn('currencies', 'grade_levels')) {
                return;
            }

            $afterColumn = $schema->hasColumn('currencies', 'decimal_separator')
                ? 'decimal_separator'
                : ($schema->hasColumn('currencies', 'symbol') ? 'symbol' : 'exchange_rate');

            $table->json('grade_levels')->nullable()->after($afterColumn);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $schema = Schema::connection('tenant');

        if (! $schema->hasTable('currencies')) {
            return;
        }

        $schema->table('currencies', function (Blueprint $table) use ($schema) {
            if ($schema->hasColumn('currencies', 'grade_levels')) {
                $table->dropColumn('grade_levels');
            }
        });
    }
};
