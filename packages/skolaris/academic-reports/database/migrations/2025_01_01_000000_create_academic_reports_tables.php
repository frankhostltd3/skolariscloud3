<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Use existing 'terms' table if available, otherwise create 'academic_terms'
        if (!Schema::hasTable('terms') && !Schema::hasTable('academic_terms')) {
            Schema::create('academic_terms', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->date('start_date');
                $table->date('end_date');
                $table->timestamps();
            });
        }

        // Use existing 'subjects' table if available
        if (!Schema::hasTable('subjects')) {
            Schema::create('subjects', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('academic_reports')) {
            Schema::create('academic_reports', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                // Flexible foreign key for term (supports 'terms' or 'academic_terms')
                $table->unsignedBigInteger('term_id'); 
                $table->string('class_name');
                $table->float('total_marks')->nullable();
                $table->float('average_score')->nullable();
                $table->integer('rank')->nullable();
                $table->text('class_teacher_remarks')->nullable();
                $table->text('principal_remarks')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('report_marks')) {
            Schema::create('report_marks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('report_id')->constrained('academic_reports')->onDelete('cascade');
                $table->unsignedBigInteger('subject_id'); // Flexible FK
                $table->float('score');
                $table->string('grade')->nullable();
                $table->string('remarks')->nullable();
                $table->timestamps();
            });
        }

        // Create student_fees table if it doesn't exist (independent of 'fees' table)
        if (!Schema::hasTable('student_fees')) {
            Schema::create('student_fees', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('term_id');
                $table->decimal('amount_due', 10, 2);
                $table->decimal('amount_paid', 10, 2)->default(0);
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }
        
        // Add photo column to users table if it doesn't exist
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'photo_path')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('photo_path')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('student_fees');
        Schema::dropIfExists('report_marks');
        Schema::dropIfExists('academic_reports');
        // Do not drop shared tables like subjects/terms if they might be used by others
    }
};