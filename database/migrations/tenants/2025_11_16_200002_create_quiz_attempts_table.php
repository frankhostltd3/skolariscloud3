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
        Schema::connection('tenant')->create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->decimal('score_auto', 8, 2)->default(0);
            $table->decimal('score_manual', 8, 2)->default(0);
            $table->decimal('score_total', 8, 2)->default(0);
            $table->integer('minutes_late')->default(0);
            $table->enum('status', ['in_progress', 'submitted', 'graded', 'expired'])->default('in_progress');
            $table->text('answers')->nullable(); // JSON
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->index('school_id');
            $table->index('quiz_id');
            $table->index('student_id');
            $table->index('submitted_at');
            $table->index('status');
            $table->index('minutes_late');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('quiz_attempts');
    }
};
