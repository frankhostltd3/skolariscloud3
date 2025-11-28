<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('class_streams')) {
            return;
        }

        Schema::table('class_streams', function (Blueprint $table) {
            if (!Schema::hasColumn('class_streams', 'max_capacity')) {
                $table->unsignedSmallInteger('max_capacity')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('class_streams')) {
            return;
        }

        Schema::table('class_streams', function (Blueprint $table) {
            if (Schema::hasColumn('class_streams', 'max_capacity')) {
                $table->dropColumn('max_capacity');
            }
        });
    }
};
