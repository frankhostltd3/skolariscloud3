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
        if (!Schema::hasTable('subjects')) {
            Schema::create('subjects', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id');
                $table->unsignedBigInteger('education_level_id')->nullable();
                $table->string('name'); // e.g., "Mathematics", "English"
                $table->string('code')->nullable(); // e.g., "MATH", "ENG"
                $table->text('description')->nullable();
                $table->string('category')->nullable(); // e.g., "Science", "Arts", "Languages"
                $table->boolean('is_compulsory')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('pass_mark')->nullable(); // Minimum passing grade
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
        Schema::dropIfExists('subjects');
    }
};
