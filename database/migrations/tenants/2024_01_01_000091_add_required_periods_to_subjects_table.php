<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'tenant';

    public function up(): void
    {
        $afterColumn = Schema::hasColumn('subjects', 'credit_hours')
            ? 'credit_hours'
            : (Schema::hasColumn('subjects', 'pass_mark') ? 'pass_mark' : null);

        Schema::table('subjects', function (Blueprint $table) use ($afterColumn) {
            if (!Schema::hasColumn('subjects', 'required_periods_per_week')) {
                $column = $table->unsignedTinyInteger('required_periods_per_week')->nullable();
                if ($afterColumn) {
                    $column->after($afterColumn);
                }
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
