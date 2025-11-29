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
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('cancellation_reason')->nullable()->after('notes');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null')->after('cancellation_reason');
            $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            $table->text('deletion_reason')->nullable()->after('cancelled_at');
            $table->text('revision_reason')->nullable()->after('deletion_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn(['cancellation_reason', 'cancelled_by', 'cancelled_at', 'deletion_reason', 'revision_reason']);
        });
    }
};
