<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('settings') || ! Schema::hasColumn('settings', 'tenant_id')) {
            return;
        }

        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS settings_key_tenant_id_unique');
            DB::statement('DROP INDEX IF EXISTS settings_category_tenant_id_index');
        } else {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropUnique('settings_key_tenant_id_unique');
                $table->dropIndex('settings_category_tenant_id_index');
            });
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `settings` MODIFY `tenant_id` VARCHAR(36) NULL');
        } elseif ($driver === 'sqlite') {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('tenant_id_tmp', 36)->nullable()->after('tenant_id');
            });

            DB::statement('UPDATE settings SET tenant_id_tmp = tenant_id');

            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });

            DB::statement('ALTER TABLE settings RENAME COLUMN tenant_id_tmp TO tenant_id');
        } else {
            throw new \RuntimeException(sprintf('Unsupported database driver [%s] for settings tenant_id migration.', $driver));
        }

        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'category') || ! Schema::hasColumn('settings', 'tenant_id')) {
                return;
            }

            $table->index(['category', 'tenant_id'], 'settings_category_tenant_id_index');
            $table->unique(['key', 'tenant_id'], 'settings_key_tenant_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('settings') || ! Schema::hasColumn('settings', 'tenant_id')) {
            return;
        }

        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS settings_key_tenant_id_unique');
            DB::statement('DROP INDEX IF EXISTS settings_category_tenant_id_index');
        } else {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropUnique('settings_key_tenant_id_unique');
                $table->dropIndex('settings_category_tenant_id_index');
            });
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `settings` MODIFY `tenant_id` BIGINT UNSIGNED NULL');
        } elseif ($driver === 'sqlite') {
            Schema::table('settings', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id_tmp')->nullable()->after('tenant_id');
            });

            DB::statement('UPDATE settings SET tenant_id_tmp = tenant_id');

            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });

            DB::statement('ALTER TABLE settings RENAME COLUMN tenant_id_tmp TO tenant_id');
        } else {
            throw new \RuntimeException(sprintf('Unsupported database driver [%s] for settings tenant_id rollback.', $driver));
        }

        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'category') || ! Schema::hasColumn('settings', 'tenant_id')) {
                return;
            }

            $table->index(['category', 'tenant_id'], 'settings_category_tenant_id_index');
            $table->unique(['key', 'tenant_id'], 'settings_key_tenant_id_unique');
        });
    }
};
