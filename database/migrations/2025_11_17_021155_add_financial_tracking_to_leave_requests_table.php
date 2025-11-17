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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->decimal('daily_rate', 12, 2)->nullable()->after('days_requested')->comment('Employee daily salary rate at time of request');
            $table->decimal('financial_impact', 12, 2)->nullable()->after('daily_rate')->comment('Total financial impact (deduction for unpaid, value for paid)');
            $table->boolean('is_paid')->default(true)->after('financial_impact')->comment('Whether this leave type is paid or unpaid');
            $table->text('financial_notes')->nullable()->after('manager_comment')->comment('Detailed financial calculation notes');
            $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete()->comment('User who approved/declined the request');
            $table->timestamp('approved_at')->nullable()->after('approved_by')->comment('When the request was approved/declined');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'daily_rate',
                'financial_impact',
                'is_paid',
                'financial_notes',
                'approved_by',
                'approved_at'
            ]);
        });
    }
};
