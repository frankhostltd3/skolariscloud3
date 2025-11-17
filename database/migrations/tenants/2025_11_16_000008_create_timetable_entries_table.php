<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('tenant')->create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('class_stream_id')->nullable();
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->unsignedTinyInteger('day_of_week'); // 1=Monday, 7=Sunday
            $table->time('starts_at');
            $table->time('ends_at');
            $table->string('room', 50)->nullable();
            $table->string('notes', 500)->nullable();
            $table->timestamps();

            // Indexes
            $table->index('school_id');
            $table->index(['school_id', 'class_id']);
            $table->index(['school_id', 'day_of_week']);
            $table->index(['school_id', 'teacher_id']);
            $table->index(['class_id', 'day_of_week', 'starts_at']); // For conflict detection

            // Foreign keys
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('class_stream_id')->references('id')->on('class_streams')->onDelete('set null');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('timetable_entries');
    }
};
