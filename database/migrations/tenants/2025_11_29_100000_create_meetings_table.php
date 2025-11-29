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
            $table->string('meeting_type')->default('in-person'); // online, in-person
            $table->string('platform')->nullable();
            $table->string('meeting_link')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->json('participants')->nullable(); // Array of user IDs
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
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
