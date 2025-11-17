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
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->onDelete('set null');
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->onDelete('set null');
            $table->foreignId('term_id')->nullable()->constrained('terms')->onDelete('set null');
            $table->string('title'); // e.g., "Final Report - Math Grade 10"
            $table->string('file_path'); // Path to the stored PDF file
            $table->string('file_name'); // Original filename
            $table->unsignedInteger('file_size'); // File size in bytes
            $table->string('mime_type')->default('application/pdf');
            $table->enum('report_type', ['semester', 'term', 'annual', 'custom'])->default('semester');
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade'); // Who generated it
            $table->timestamp('generated_at')->useCurrent();
            $table->json('metadata')->nullable(); // Additional data like grade counts, subjects, etc.
            $table->timestamps();

            $table->index(['student_id', 'academic_year_id']);
            $table->index(['student_id', 'report_type']);
            $table->index(['generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};