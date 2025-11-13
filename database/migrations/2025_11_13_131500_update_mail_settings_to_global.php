<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mail_settings', function (Blueprint $table): void {
            if (Schema::hasColumn('mail_settings', 'school_id')) {
                $table->dropForeign(['school_id']);
                $table->dropUnique(['school_id']);
                $table->dropColumn('school_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mail_settings', function (Blueprint $table): void {
            if (! Schema::hasColumn('mail_settings', 'school_id')) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
                $table->unique('school_id');
            }
        });
    }
};
