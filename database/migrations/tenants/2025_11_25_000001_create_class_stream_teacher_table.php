<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('class_stream_teacher')) {
            Schema::create('class_stream_teacher', function (Blueprint $table) {
                $table->id();
                $table->foreignId('class_stream_id')->constrained('class_streams')->cascadeOnDelete();
                $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
                $table->string('academic_year')->nullable();
                $table->timestamps();

                $table->unique(['class_stream_id', 'teacher_id', 'academic_year'], 'class_stream_teacher_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('class_stream_teacher');
    }
};
