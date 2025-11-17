<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('lesson_date');
            $table->integer('duration_minutes')->default(40);
            $table->text('objectives')->nullable(); // Learning objectives
            $table->text('materials_needed')->nullable();
            $table->text('introduction')->nullable();
            $table->text('main_content')->nullable();
            $table->text('activities')->nullable();
            $table->text('assessment')->nullable();
            $table->text('homework')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'published', 'completed'])->default('draft');
            $table->boolean('is_template')->default(false);
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['teacher_id', 'lesson_date']);
            $table->index(['class_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_plans');
    }
};
