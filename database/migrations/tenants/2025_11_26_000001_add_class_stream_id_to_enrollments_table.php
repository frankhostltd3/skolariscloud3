<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (! Schema::hasColumn('enrollments', 'class_stream_id')) {
                $table->foreignId('class_stream_id')
                    ->nullable()
                    ->after('class_id')
                    ->constrained('class_streams')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'class_stream_id')) {
                $table->dropForeign(['class_stream_id']);
                $table->dropColumn('class_stream_id');
            }
        });
    }
};
