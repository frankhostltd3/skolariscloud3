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
        if (!Schema::hasTable('education_levels')) {
            Schema::create('education_levels', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id');
                $table->string('name'); // e.g., "Primary", "O-Level", "A-Level"
                $table->string('code')->nullable(); // e.g., "P", "O", "A"
                $table->string('description')->nullable();
                $table->integer('min_grade')->nullable(); // Minimum grade/year level
                $table->integer('max_grade')->nullable(); // Maximum grade/year level
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->index('school_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_levels');
    }
};
