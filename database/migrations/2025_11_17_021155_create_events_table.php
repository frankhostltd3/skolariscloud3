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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->string('location')->nullable();
            $table->string('event_type', 20)->default('general'); // general, holiday, exam, sports, cultural, academic
            $table->string('priority', 10)->default('normal'); // low, normal, high
            $table->json('target_audience')->nullable(); // ['students', 'teachers', 'parents', 'staff']
            $table->boolean('is_all_day')->default(false);
            $table->string('color', 7)->default('#1e40af'); // hex color
            $table->unsignedBigInteger('created_by');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['event_type', 'start_date']);
            $table->index('created_by');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
