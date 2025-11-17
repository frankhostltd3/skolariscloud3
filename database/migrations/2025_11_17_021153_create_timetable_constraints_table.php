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
        Schema::create('timetable_constraints', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // teacher_availability, room_availability, etc.
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('constraints'); // JSON data for constraint details
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_constraints');
    }
};
