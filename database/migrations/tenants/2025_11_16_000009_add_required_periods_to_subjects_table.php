<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'required_periods_per_week')) {
                $table->unsignedTinyInteger('required_periods_per_week')->nullable()->after('credit_hours');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'required_periods_per_week')) {
                $table->dropColumn('required_periods_per_week');
            }
        });
    }
};
