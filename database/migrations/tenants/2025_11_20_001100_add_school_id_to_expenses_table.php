<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'school_id')) {
                $table->unsignedBigInteger('school_id')->nullable()->after('id');
                $table->index('school_id');
            }
        });

        if (Schema::hasColumn('expenses', 'school_id')) {
            $schoolId = config('tenant.school_id');

            if (! $schoolId && function_exists('tenant') && tenant()) {
                $schoolId = tenant('id') ?? data_get(tenant(), 'id');
            }

            if ($schoolId) {
                DB::table('expenses')
                    ->whereNull('school_id')
                    ->update(['school_id' => $schoolId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'school_id')) {
                $table->dropIndex('expenses_school_id_index');
                $table->dropColumn('school_id');
            }
        });
    }
};
