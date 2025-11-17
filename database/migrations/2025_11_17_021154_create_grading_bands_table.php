<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grading_bands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_scheme_id')->constrained('grading_schemes')->cascadeOnDelete();
            $table->string('code')->nullable(); // e.g., A, A1, Distinction
            $table->string('label'); // readable description
            $table->unsignedSmallInteger('min_score'); // inclusive
            $table->unsignedSmallInteger('max_score'); // inclusive
            $table->unsignedTinyInteger('order')->default(0);
            $table->json('awards')->nullable(); // optional awards configuration
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_bands');
    }
};
