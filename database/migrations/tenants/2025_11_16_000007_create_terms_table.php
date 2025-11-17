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
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('name', 50); // Reduced to 50 for unique index constraint
            $table->string('code', 20)->nullable();
            $table->string('academic_year', 10); // Reduced to 10 (e.g., "2024/2025", "2025")
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->boolean('is_current')->default(false); // Only one term can be current per school
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->index('school_id');
            $table->index(['school_id', 'is_current']);
            $table->index(['school_id', 'academic_year']);
            // Unique constraint: school_id (8 bytes) + name (50*4=200 bytes) + academic_year (10*4=40 bytes) = 248 bytes < 1000
            $table->unique(['school_id', 'name', 'academic_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};
