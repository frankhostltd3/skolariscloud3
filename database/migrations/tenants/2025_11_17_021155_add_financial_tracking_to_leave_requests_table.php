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
        if (! Schema::hasTable('leave_requests')) {
            return;
        }

        Schema::table('leave_requests', function (Blueprint $table) {
            $afterDaysRequested = Schema::hasColumn('leave_requests', 'days_requested') ? 'days_requested' : 'end_date';

            if (! Schema::hasColumn('leave_requests', 'daily_rate')) {
                $table->decimal('daily_rate', 12, 2)->nullable()->after($afterDaysRequested)->comment('Employee daily salary rate at time of request');
            }

            if (! Schema::hasColumn('leave_requests', 'financial_impact')) {
                $table->decimal('financial_impact', 12, 2)->nullable()->after('daily_rate')->comment('Total financial impact (deduction for unpaid, value for paid)');
            }

            if (! Schema::hasColumn('leave_requests', 'is_paid')) {
                $table->boolean('is_paid')->default(true)->after('financial_impact')->comment('Whether this leave type is paid or unpaid');
            }

            if (! Schema::hasColumn('leave_requests', 'financial_notes')) {
                $table->text('financial_notes')->nullable()->after('manager_comment')->comment('Detailed financial calculation notes');
            }

            if (! Schema::hasColumn('leave_requests', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete()->comment('User who approved/declined the request');
            }

            if (! Schema::hasColumn('leave_requests', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by')->comment('When the request was approved/declined');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('leave_requests')) {
            return;
        }

        Schema::table('leave_requests', function (Blueprint $table) {
            if (Schema::hasColumn('leave_requests', 'approved_by')) {
                $table->dropForeign(['approved_by']);
            }

            $columns = collect([
                'daily_rate',
                'financial_impact',
                'is_paid',
                'financial_notes',
                'approved_by',
                'approved_at',
            ])->filter(fn ($column) => Schema::hasColumn('leave_requests', $column))->all();

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
