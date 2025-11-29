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
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('user_id'); // Author
            $table->string('title');
            $table->string('slug');
            $table->text('content')->nullable();

            // Context (e.g., Class, Subject, or null for General)
            $table->string('context_type')->nullable();
            $table->unsignedBigInteger('context_id')->nullable();

            $table->enum('status', ['active', 'closed', 'blocked'])->default('active');
            $table->unsignedBigInteger('moderator_id')->nullable(); // Assigned moderator

            $table->boolean('is_pinned')->default(false);
            $table->unsignedBigInteger('views_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('moderator_id')->references('id')->on('users')->onDelete('set null');

            $table->index(['school_id', 'context_type', 'context_id']);
        });

        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('forum_thread_id');
            $table->unsignedBigInteger('user_id');
            $table->text('content');

            $table->unsignedBigInteger('parent_id')->nullable(); // For nested replies
            $table->boolean('is_solution')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('forum_thread_id')->references('id')->on('forum_threads')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('forum_posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_threads');
    }
};
