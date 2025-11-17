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
        if (!Schema::hasTable('class_subjects')) {
            Schema::create('class_subjects', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('class_id');
                $table->unsignedBigInteger('subject_id');
                $table->unsignedBigInteger('teacher_id')->nullable(); // Primary teacher for this subject in this class
                $table->integer('periods_per_week')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
                $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
                $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');

                $table->unique(['class_id', 'subject_id']);
                $table->index('class_id');
                $table->index('subject_id');
                $table->index('teacher_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_subjects');
    }
};
