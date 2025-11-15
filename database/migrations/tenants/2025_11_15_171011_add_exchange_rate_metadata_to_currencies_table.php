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
        Schema::connection('tenant')->table('currencies', function (Blueprint $table) {
            $table->boolean('auto_update_enabled')->default(false)->after('is_active');
            $table->timestamp('last_updated_at')->nullable()->after('auto_update_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->table('currencies', function (Blueprint $table) {
            $table->dropColumn(['auto_update_enabled', 'last_updated_at']);
        });
    }
};
