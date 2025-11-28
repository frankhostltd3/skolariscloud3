<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'school_id')) {
                $table->unsignedBigInteger('school_id')->nullable()->after('id');
                $table->index('school_id');
            }
        });

        if (Schema::hasColumn('invoices', 'school_id')) {
            $schoolId = config('tenant.school_id');

            if (! $schoolId && function_exists('tenant') && tenant()) {
                $schoolId = tenant('id') ?? data_get(tenant(), 'id');
            }

            if ($schoolId) {
                DB::table('invoices')
                    ->whereNull('school_id')
                    ->update(['school_id' => $schoolId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'school_id')) {
                $table->dropIndex('invoices_school_id_index');
                $table->dropColumn('school_id');
            }
        });
    }
};
