<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('tenant')->create('lesson_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->string('title');
            $table->date('lesson_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->json('objectives')->nullable();
            $table->json('materials_needed')->nullable();
            $table->json('activities')->nullable();
            $table->longText('introduction')->nullable();
            $table->longText('main_content')->nullable();
            $table->longText('assessment')->nullable();
            $table->longText('homework')->nullable();
            $table->longText('notes')->nullable();
            $table->string('status')->default('draft');
            $table->string('review_status')->default('not_submitted');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_feedback')->nullable();
            $table->boolean('is_template')->default(false);
            $table->timestamp('delivered_at')->nullable();
            $table->boolean('requires_revision')->default(false);
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['teacher_id', 'lesson_date']);
            $table->index('status');
            $table->index('review_status');
            $table->index('is_template');
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('lesson_plans');
    }
};
