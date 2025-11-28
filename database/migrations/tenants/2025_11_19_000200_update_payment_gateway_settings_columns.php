<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        if (! Schema::hasTable('payment_gateway_settings')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement('ALTER TABLE `payment_gateway_settings` MODIFY `config` LONGTEXT NULL');
        DB::statement('ALTER TABLE `payment_gateway_settings` MODIFY `meta` LONGTEXT NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('payment_gateway_settings')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement('ALTER TABLE `payment_gateway_settings` MODIFY `config` JSON NULL');
        DB::statement('ALTER TABLE `payment_gateway_settings` MODIFY `meta` JSON NULL');
    }
};
