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
        if (!Schema::connection('tenant')->hasTable('classes')) {
            Schema::connection('tenant')->create('classes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                $table->string('name');
                $table->string('grade_level')->nullable();
                $table->string('stream')->nullable();
                $table->integer('capacity')->default(50);
                $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('room_number')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('school_id');
                $table->index('teacher_id');
                $table->index('grade_level');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('classes');
    }
};
