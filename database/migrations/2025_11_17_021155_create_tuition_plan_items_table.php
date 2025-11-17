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
        if (!Schema::hasTable('tuition_plan_items')) {
            Schema::create('tuition_plan_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tuition_plan_id');
                $table->unsignedBigInteger('fee_id');
                $table->string('description');
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 10, 2);
                $table->decimal('total_amount', 10, 2);
                $table->decimal('tax_rate', 5, 2)->default(0);
                $table->decimal('tax_amount', 10, 2)->default(0);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->decimal('net_amount', 10, 2);
                $table->timestamps();

                $table->foreign('tuition_plan_id')->references('id')->on('tuition_plans')->onDelete('cascade');
                $table->foreign('fee_id')->references('id')->on('fees');
                $table->index('tuition_plan_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuition_plan_items');
    }
};
