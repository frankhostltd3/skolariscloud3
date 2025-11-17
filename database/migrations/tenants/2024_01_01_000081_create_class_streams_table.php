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
        if (!Schema::hasTable('class_streams')) {
            Schema::create('class_streams', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('class_id');
                $table->string('name'); // e.g., "A", "B", "East", "West"
                $table->string('code')->nullable();
                $table->text('description')->nullable();
                $table->integer('capacity')->nullable();
                $table->integer('active_students_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
                $table->index('class_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_streams');
    }
};
