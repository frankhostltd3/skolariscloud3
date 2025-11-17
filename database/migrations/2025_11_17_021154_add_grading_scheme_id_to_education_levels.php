<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('education_levels', function (Blueprint $table) {
            if (!Schema::hasColumn('education_levels', 'grading_scheme_id')) {
                $table->foreignId('grading_scheme_id')->nullable()->after('order')
                    ->constrained('grading_schemes')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('education_levels', function (Blueprint $table) {
            if (Schema::hasColumn('education_levels', 'grading_scheme_id')) {
                $table->dropForeign(['grading_scheme_id']);
                $table->dropColumn('grading_scheme_id');
            }
        });
    }
};
