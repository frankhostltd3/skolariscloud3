<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('grades')) {
            return;
        }

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->string('term')->nullable();
            $table->string('score');
            $table->date('awarded_on')->nullable();
            $table->timestamps();
            $table->unique(['student_id','subject_id','term']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
