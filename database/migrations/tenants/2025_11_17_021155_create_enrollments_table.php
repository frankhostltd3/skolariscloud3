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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('enrollment_date');
            $table->string('status')->default('active'); // active, dropped, transferred, completed
            $table->decimal('fees_paid', 10, 2)->default(0.00);
            $table->decimal('fees_total', 10, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->foreignId('enrolled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['student_id', 'class_id', 'academic_year_id']);
            $table->index(['class_id', 'status']);
            $table->index(['academic_year_id', 'status']);
            $table->index(['enrollment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};