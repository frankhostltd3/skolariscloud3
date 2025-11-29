<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_behaviours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reporter_id')->constrained('users')->nullOnDelete(); // Teacher or staff
            $table->string('type'); // 'positive', 'negative'
            $table->string('category'); // e.g., 'Attendance', 'Discipline', 'Academic', 'Social'
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('points')->default(0); // For merit/demerit systems
            $table->date('incident_date');
            $table->text('action_taken')->nullable(); // For negative incidents
            $table->string('status')->default('recorded'); // 'recorded', 'pending_review', 'resolved'
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('type')->default('news'); // 'news', 'alert', 'event', 'reminder'
            $table->json('target_audience')->nullable(); // ['role:parent', 'class:5', 'school:all']
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->string('priority')->default('normal'); // 'low', 'normal', 'high', 'urgent'
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('student_behaviours');
    }
};
