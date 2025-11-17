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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(30);
            $table->string('meeting_type', 20)->default('general'); // general, parent_teacher, counseling, academic
            $table->string('platform', 20)->nullable(); // zoom, google_meet, teams, physical
            $table->string('meeting_link')->nullable();
            $table->string('location')->nullable(); // for physical meetings
            $table->string('status', 20)->default('scheduled'); // scheduled, completed, cancelled, rescheduled
            $table->unsignedBigInteger('organizer_id'); // teacher/admin who scheduled
            $table->json('participants')->nullable(); // array of user IDs
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['meeting_type', 'scheduled_at']);
            $table->index(['organizer_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
