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
        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'on_leave', 'sick_leave', 'official_duty'])->default('present');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->integer('minutes_late')->default(0);
            $table->decimal('hours_worked', 5, 2)->default(0);
            $table->text('leave_reason')->nullable();
            $table->string('leave_document')->nullable();
            $table->boolean('approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('school_id');
            $table->index('staff_id');
            $table->index('attendance_date');
            $table->index('status');
            $table->index(['school_id', 'attendance_date']);
            $table->index(['staff_id', 'attendance_date']);
            $table->unique(['staff_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_attendance');
    }
};
