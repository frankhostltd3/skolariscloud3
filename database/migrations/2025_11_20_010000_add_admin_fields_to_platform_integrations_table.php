<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('platform_integrations')) {
            return;
        }

        Schema::table('platform_integrations', function (Blueprint $table) {
            if (! Schema::hasColumn('platform_integrations', 'managed_by_admin')) {
                $table->boolean('managed_by_admin')->default(false)->after('is_enabled');
            }
            if (! Schema::hasColumn('platform_integrations', 'last_tested_at')) {
                $table->timestamp('last_tested_at')->nullable()->after('token_expires_at');
            }
            if (! Schema::hasColumn('platform_integrations', 'status')) {
                $table->string('status')->default('needs_configuration')->after('last_tested_at');
            }
            if (! Schema::hasColumn('platform_integrations', 'status_message')) {
                $table->text('status_message')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('platform_integrations')) {
            return;
        }

        Schema::table('platform_integrations', function (Blueprint $table) {
            if (Schema::hasColumn('platform_integrations', 'status_message')) {
                $table->dropColumn('status_message');
            }
            if (Schema::hasColumn('platform_integrations', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('platform_integrations', 'last_tested_at')) {
                $table->dropColumn('last_tested_at');
            }
            if (Schema::hasColumn('platform_integrations', 'managed_by_admin')) {
                $table->dropColumn('managed_by_admin');
            }
        });
    }
};
