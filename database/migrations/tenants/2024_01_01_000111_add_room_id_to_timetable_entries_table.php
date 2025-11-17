<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('timetable_entries', 'room_id')) {
                $table->unsignedBigInteger('room_id')->nullable()->after('teacher_id');
                $table->index(['school_id', 'room_id']);
                $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {
            if (Schema::hasColumn('timetable_entries', 'room_id')) {
                $table->dropForeign(['room_id']);
                $table->dropIndex(['school_id', 'room_id']);
                $table->dropColumn('room_id');
            }
        });
    }
};
