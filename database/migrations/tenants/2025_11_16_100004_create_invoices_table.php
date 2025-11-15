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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('fee_structure_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance', 15, 2);
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('status')->default('unpaid'); // unpaid, partial, paid, overdue
            $table->string('academic_year');
            $table->string('term')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'student_id']);
            $table->index(['school_id', 'status']);
            $table->index('due_date');
            $table->index('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
