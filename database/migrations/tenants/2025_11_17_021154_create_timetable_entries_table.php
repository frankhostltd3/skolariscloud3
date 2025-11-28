<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('timetable_entries')) return;
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('day_of_week'); // 1..7
            $table->time('starts_at');
            $table->time('ends_at');
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('class_stream_id')->nullable()->constrained('class_streams')->nullOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->string('room', 50)->nullable();
            $table->string('notes', 500)->nullable();
            $table->timestamps();

            // Short, explicit index name to avoid MySQL identifier length limit
            $table->index(['class_id','class_stream_id','day_of_week','starts_at'], 'te_class_stream_dow_start_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_entries');
    }
};
