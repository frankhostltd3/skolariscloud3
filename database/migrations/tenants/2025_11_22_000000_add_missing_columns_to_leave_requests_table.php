<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_requests', 'days_requested')) {
                $table->integer('days_requested')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('leave_requests', 'daily_rate')) {
                $table->decimal('daily_rate', 10, 2)->nullable()->after('manager_comment');
            }
            if (!Schema::hasColumn('leave_requests', 'financial_impact')) {
                $table->decimal('financial_impact', 10, 2)->nullable()->after('daily_rate');
            }
            if (!Schema::hasColumn('leave_requests', 'is_paid')) {
                $table->boolean('is_paid')->default(true)->after('financial_impact');
            }
            if (!Schema::hasColumn('leave_requests', 'financial_notes')) {
                $table->text('financial_notes')->nullable()->after('is_paid');
            }
            if (!Schema::hasColumn('leave_requests', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('financial_notes');
            }
            if (!Schema::hasColumn('leave_requests', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn([
                'days_requested',
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
