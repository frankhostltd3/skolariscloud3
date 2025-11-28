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
            // Rename decimals to decimal_places for consistency
            if ($schema->hasColumn('currencies', 'decimals') && ! $schema->hasColumn('currencies', 'decimal_places')) {
                $table->renameColumn('decimals', 'decimal_places');
            }

            // Rename enabled to is_active for consistency
            if ($schema->hasColumn('currencies', 'enabled') && ! $schema->hasColumn('currencies', 'is_active')) {
                $table->renameColumn('enabled', 'is_active');
            }

            // Add new fields
            if (! $schema->hasColumn('currencies', 'symbol_position')) {
                $afterColumn = $schema->hasColumn('currencies', 'symbol')
                    ? 'symbol'
                    : ($schema->hasColumn('currencies', 'name') ? 'name' : 'code');

                $table->enum('symbol_position', ['before', 'after'])->default('before')->after($afterColumn);
            }

            if (! $schema->hasColumn('currencies', 'thousands_separator')) {
                $afterColumn = $schema->hasColumn('currencies', 'decimal_places') ? 'decimal_places' : 'exchange_rate';
                $table->string('thousands_separator', 5)->default(',')->after($afterColumn);
            }

            if (! $schema->hasColumn('currencies', 'decimal_separator')) {
                $afterColumn = $schema->hasColumn('currencies', 'thousands_separator') ? 'thousands_separator' : 'exchange_rate';
                $table->string('decimal_separator', 5)->default('.')->after($afterColumn);
            }

            if (! $schema->hasColumn('currencies', 'is_default')) {
                $afterColumn = $schema->hasColumn('currencies', 'is_active') ? 'is_active' : 'exchange_rate';
                $table->boolean('is_default')->default(false)->after($afterColumn);
            }

            // Index handling skipped for sqlite compatibility
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
            // Reverse index changes skipped for sqlite compatibility

            $columns = collect(['symbol_position', 'thousands_separator', 'decimal_separator', 'is_default'])
                ->filter(fn ($column) => $schema->hasColumn('currencies', $column))
                ->all();

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }

            // Rename back
            if ($schema->hasColumn('currencies', 'decimal_places') && ! $schema->hasColumn('currencies', 'decimals')) {
                $table->renameColumn('decimal_places', 'decimals');
            }

            if ($schema->hasColumn('currencies', 'is_active') && ! $schema->hasColumn('currencies', 'enabled')) {
                $table->renameColumn('is_active', 'enabled');
            }
        });
    }
};
