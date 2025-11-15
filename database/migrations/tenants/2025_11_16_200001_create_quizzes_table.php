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
        Schema::connection('tenant')->create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->integer('total_marks')->default(100);
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_late_submission')->default(false);
            $table->integer('late_penalty_percent')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('teacher_id');
            $table->index('class_id');
            $table->index(['start_at', 'end_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('quizzes');
    }
};
