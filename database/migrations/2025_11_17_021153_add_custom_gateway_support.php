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
        // Add custom_config column to allow custom gateway definitions
        // Central/Landlord database
        $connection = Schema::connection(config('tenancy.database.central_connection'));
        
        if (!$connection->hasColumn('payment_gateway_configs', 'custom_config')) {
            $connection->table('payment_gateway_configs', function (Blueprint $table) {
                $table->json('custom_config')->nullable()->after('settings');
                $table->boolean('is_custom')->default(false)->after('context');
            });
        }

        // This will be run for tenant databases separately via tenant migrations
        // Not all tenants may have this table yet, so we check first
        if (Schema::hasTable('tenant_payment_gateway_configs') && 
            !Schema::hasColumn('tenant_payment_gateway_configs', 'custom_config')) {
            Schema::table('tenant_payment_gateway_configs', function (Blueprint $table) {
                $table->json('custom_config')->nullable()->after('settings');
                $table->boolean('is_custom')->default(false)->after('gateway');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(config('tenancy.database.central_connection'))
            ->table('payment_gateway_configs', function (Blueprint $table) {
                $table->dropColumn(['custom_config', 'is_custom']);
            });

        if (Schema::hasTable('tenant_payment_gateway_configs')) {
            Schema::table('tenant_payment_gateway_configs', function (Blueprint $table) {
                $table->dropColumn(['custom_config', 'is_custom']);
            });
        }
    }
};
