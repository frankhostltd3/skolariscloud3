<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fee_invoice_id');
            $table->unsignedBigInteger('student_id');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default(config('currency.default', 'UGX'));
            $table->enum('method', ['cash', 'bank', 'mtn', 'airtel', 'card'])->default('cash');
            $table->string('reference')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('status')->default('confirmed'); // confirmed, pending, failed
            $table->json('meta')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->timestamps();

            $table->foreign('fee_invoice_id')->references('id')->on('fee_invoices')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};