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
        Schema::create('fee_reminders', function (Blueprint $table) {
            $table->id();
            $table->json('fee_ids'); // Array of fee IDs included in reminder
            $table->enum('reminder_type', ['overdue', 'upcoming', 'all']);
            $table->enum('target_audience', ['all_students', 'specific_class', 'specific_students']);
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
            $table->json('student_ids')->nullable(); // Array of specific student IDs
            $table->text('custom_message')->nullable();
            $table->boolean('sent_via_email')->default(false);
            $table->boolean('sent_via_sms')->default(false);
            $table->integer('recipient_count')->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('sent_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_reminders');
    }
};
