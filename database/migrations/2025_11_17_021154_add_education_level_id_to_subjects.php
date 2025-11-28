<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('subjects')) {
            return;
        }

        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'education_level_id')) {
                $table->foreignId('education_level_id')->nullable()->after('name')->constrained('education_levels')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('subjects')) {
            return;
        }

        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'education_level_id')) {
                $table->dropConstrainedForeignId('education_level_id');
            }
        });
    }
};
