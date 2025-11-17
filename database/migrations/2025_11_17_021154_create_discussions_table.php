<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['general', 'question', 'announcement', 'poll'])->default('general');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('allow_replies')->default(true);
            $table->boolean('requires_approval')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('replies_count')->default(0);
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['class_id', 'is_pinned']);
            $table->index(['teacher_id', 'created_at']);
        });

        Schema::create('discussion_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Can be student or teacher
            $table->foreignId('parent_id')->nullable()->constrained('discussion_replies')->onDelete('cascade'); // For nested replies
            $table->text('content');
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_best_answer')->default(false);
            $table->integer('likes_count')->default(0);
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['discussion_id', 'created_at']);
        });

        Schema::create('discussion_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('likeable'); // Can like discussions or replies
            $table->timestamps();

            $table->unique(['user_id', 'likeable_id', 'likeable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_likes');
        Schema::dropIfExists('discussion_replies');
        Schema::dropIfExists('discussions');
    }
};
