<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('grades')) {
            return;
        }

        Schema::table('grades', function (Blueprint $table) {
            if (!Schema::hasColumn('grades', 'band_code')) {
                $table->string('band_code', 20)->nullable()->after('score');
            }
            if (!Schema::hasColumn('grades', 'band_label')) {
                $table->string('band_label', 190)->nullable()->after('band_code');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('grades')) {
            return;
        }

        Schema::table('grades', function (Blueprint $table) {
            if (Schema::hasColumn('grades', 'band_label')) {
                $table->dropColumn('band_label');
            }
            if (Schema::hasColumn('grades', 'band_code')) {
                $table->dropColumn('band_code');
            }
        });
    }
};
