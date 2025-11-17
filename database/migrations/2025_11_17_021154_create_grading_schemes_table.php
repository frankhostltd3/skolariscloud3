<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grading_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country')->nullable(); // link logically with Education Levels/Exam Bodies
            $table->unsignedBigInteger('examination_body_id')->nullable();
            $table->boolean('is_current')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('examination_body_id')->references('id')->on('examination_bodies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_schemes');
    }
};
