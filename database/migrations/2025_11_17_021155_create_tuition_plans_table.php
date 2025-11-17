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
        if (!Schema::hasTable('tuition_plans')) {
            Schema::create('tuition_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('academic_year');
                $table->string('grade_level');
                $table->decimal('total_amount', 10, 2);
                $table->string('currency', 3)->default('USD');
                $table->integer('installment_count')->default(1);
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by');
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users');
                $table->index(['academic_year']);
                $table->index(['grade_level']);
                $table->index('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuition_plans');
    }
};
