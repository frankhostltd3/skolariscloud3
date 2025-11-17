<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('education_levels', function (Blueprint $table) {
            if (!Schema::hasColumn('education_levels', 'name_translations')) {
                $table->json('name_translations')->nullable()->after('name');
            }
            if (!Schema::hasColumn('education_levels', 'min_year')) {
                $table->unsignedTinyInteger('min_year')->nullable()->after('code');
            }
            if (!Schema::hasColumn('education_levels', 'max_year')) {
                $table->unsignedTinyInteger('max_year')->nullable()->after('min_year');
            }
        });
    }

    public function down(): void
    {
        Schema::table('education_levels', function (Blueprint $table) {
            if (Schema::hasColumn('education_levels', 'name_translations')) {
                $table->dropColumn('name_translations');
            }
            if (Schema::hasColumn('education_levels', 'min_year')) {
                $table->dropColumn('min_year');
            }
            if (Schema::hasColumn('education_levels', 'max_year')) {
                $table->dropColumn('max_year');
            }
        });
    }
};
