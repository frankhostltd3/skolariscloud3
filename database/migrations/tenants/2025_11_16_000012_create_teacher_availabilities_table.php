<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'tenant';

    public function up(): void
    {
        if (!Schema::hasTable('teacher_availabilities')) {
            Schema::create('teacher_availabilities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id');
                $table->unsignedBigInteger('teacher_id');
                $table->unsignedTinyInteger('day_of_week'); // 1-7
                $table->time('available_start');
                $table->time('available_end');
                $table->timestamps();

                $table->index(['school_id', 'teacher_id', 'day_of_week']);
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_availabilities');
    }
};
