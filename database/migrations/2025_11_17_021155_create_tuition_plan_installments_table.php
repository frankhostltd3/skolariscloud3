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
        if (!Schema::hasTable('tuition_plan_installments')) {
            Schema::create('tuition_plan_installments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tuition_plan_id');
                $table->integer('installment_number');
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('amount', 10, 2);
                $table->date('due_date');
                $table->boolean('is_paid')->default(false);
                $table->timestamp('paid_at')->nullable();
                $table->string('payment_reference')->nullable();
                $table->timestamps();

                $table->foreign('tuition_plan_id')->references('id')->on('tuition_plans')->onDelete('cascade');
                $table->index(['tuition_plan_id', 'installment_number'], 'tpi_plan_number_idx');
                $table->index('is_paid');
                $table->index('due_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuition_plan_installments');
    }
};
