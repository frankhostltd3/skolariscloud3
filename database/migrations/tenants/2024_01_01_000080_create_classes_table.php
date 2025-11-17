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
        if (!Schema::hasTable('classes')) {
            Schema::create('classes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id');
                $table->unsignedBigInteger('education_level_id')->nullable();
                $table->string('name'); // e.g., "Senior 1", "Primary 5"
                $table->string('code')->nullable(); // e.g., "S1", "P5"
                $table->text('description')->nullable();
                $table->integer('capacity')->nullable(); // Maximum number of students
                $table->integer('active_students_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('education_level_id')->references('id')->on('education_levels')->onDelete('set null');
                $table->index('school_id');
                $table->index('education_level_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
