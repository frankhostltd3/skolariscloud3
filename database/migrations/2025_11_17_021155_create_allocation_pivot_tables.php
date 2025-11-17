<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Teacher → Classes allocation (teachers can teach multiple classes)
        Schema::create('class_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('academic_year')->nullable();
            $table->boolean('is_class_teacher')->default(false); // Is this the main class teacher?
            $table->timestamps();
            
            // Prevent duplicate assignments
            $table->unique(['class_id', 'teacher_id', 'academic_year']);
        });

        // Teacher → Subjects allocation (teachers can teach multiple subjects)
        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->cascadeOnDelete(); // Optional: Subject for specific class
            $table->string('academic_year')->nullable();
            $table->timestamps();
            
            // Prevent duplicate assignments
            $table->unique(['subject_id', 'teacher_id', 'class_id', 'academic_year'], 'subject_teacher_unique');
        });

        // Student → Subjects allocation (students can take multiple subjects)
        Schema::create('student_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('academic_year')->nullable();
            $table->boolean('is_core')->default(true); // Core vs elective subject
            $table->string('status')->default('active'); // active, dropped, completed
            $table->timestamps();
            
            // Prevent duplicate assignments
            $table->unique(['student_id', 'subject_id', 'academic_year']);
        });

        // Add class_stream_id to students table if not exists
        if (!Schema::hasColumn('students', 'class_stream_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->foreignId('class_stream_id')->nullable()->after('class_id')->constrained('class_streams')->nullOnDelete();
            });
        }

        // Add class_teacher_id to classes table (main class teacher)
        if (!Schema::hasColumn('classes', 'class_teacher_id')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->foreignId('class_teacher_id')->nullable()->after('name')->constrained('teachers')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_subject');
        Schema::dropIfExists('subject_teacher');
        Schema::dropIfExists('class_teacher');
        
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'class_stream_id')) {
                $table->dropForeign(['class_stream_id']);
                $table->dropColumn('class_stream_id');
            }
        });

        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'class_teacher_id')) {
                $table->dropForeign(['class_teacher_id']);
                $table->dropColumn('class_teacher_id');
            }
        });
    }
};
