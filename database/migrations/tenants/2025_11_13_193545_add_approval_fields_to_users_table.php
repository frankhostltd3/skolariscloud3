<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('approval_status')->default('approved')->after('password');
            $table->foreignId('approved_by')->nullable()->after('approval_status')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approved_at');
            $table->json('registration_data')->nullable()->after('rejection_reason');

            $table->text('suspension_reason')->nullable()->after('registration_data');
            $table->timestamp('suspended_at')->nullable()->after('suspension_reason');
            $table->foreignId('suspended_by')->nullable()->after('suspended_at')->constrained('users')->nullOnDelete();

            $table->timestamp('expelled_at')->nullable()->after('suspended_by');
            $table->text('expulsion_reason')->nullable()->after('expelled_at');
            $table->foreignId('expelled_by')->nullable()->after('expulsion_reason')->constrained('users')->nullOnDelete();

            $table->index('approval_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['suspended_by']);
            $table->dropForeign(['expelled_by']);
            $table->dropIndex(['approval_status']);

            $table->dropColumn([
                'approval_status',
                'approved_by',
                'approved_at',
                'rejection_reason',
                'registration_data',
                'suspension_reason',
                'suspended_at',
                'suspended_by',
                'expelled_at',
                'expulsion_reason',
                'expelled_by',
            ]);
        });
    }
};
