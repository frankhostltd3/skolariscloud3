<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->integer('annual_entitlement')->nullable()->after('default_days');
            $table->enum('accrual_type', ['fixed','monthly','per_period'])->nullable()->after('annual_entitlement');
            $table->decimal('accrual_rate', 8, 2)->nullable()->after('accrual_type');
            $table->integer('carry_forward_limit')->nullable()->after('accrual_rate');
            $table->boolean('paid')->default(true)->after('carry_forward_limit');
            $table->integer('max_consecutive_days')->nullable()->after('paid');
        });
    }

    public function down(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn([
                'annual_entitlement',
                'accrual_type',
                'accrual_rate',
                'carry_forward_limit',
                'paid',
                'max_consecutive_days'
            ]);
        });
    }
};
