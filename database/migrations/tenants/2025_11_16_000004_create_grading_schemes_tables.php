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
        // Grading Schemes Table
        Schema::create('grading_schemes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('name');
            $table->string('country')->nullable();
            $table->unsignedBigInteger('examination_body_id')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_current')->default(false)->comment('Currently active scheme');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('examination_body_id')->references('id')->on('examination_bodies')->onDelete('set null');

            $table->index(['school_id', 'is_current']);
            $table->index(['school_id', 'is_active']);
        });

        // Grading Bands Table
        Schema::create('grading_bands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grading_scheme_id');
            $table->string('grade'); // e.g., A, A*, 1, D1, etc.
            $table->string('label')->nullable(); // e.g., Distinction, Credit, Pass
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->decimal('grade_point', 4, 2)->nullable()->comment('GPA equivalent');
            $table->text('remarks')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('grading_scheme_id')->references('id')->on('grading_schemes')->onDelete('cascade');

            $table->index(['grading_scheme_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grading_bands');
        Schema::dropIfExists('grading_schemes');
    }
};
