<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('examination_bodies', 'name_translations')) {
            Schema::table('examination_bodies', function (Blueprint $table) {
                $table->json('name_translations')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('examination_bodies', 'name_translations')) {
            Schema::table('examination_bodies', function (Blueprint $table) {
                $table->dropColumn('name_translations');
            });
        }
    }
};
