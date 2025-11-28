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
        Schema::create('payroll_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('payroll_number')->unique(); // e.g., PAY-2025-10-001
            $table->string('period_month', 2); // 01-12
            $table->string('period_year', 4); // 2025
            $table->date('payment_date');
            
            // Salary breakdown
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('allowances', 15, 2)->default(0); // Housing, transport, etc.
            $table->decimal('bonuses', 15, 2)->default(0); // Performance, annual, etc.
            $table->decimal('overtime_pay', 15, 2)->default(0);
            $table->decimal('gross_salary', 15, 2)->default(0); // Sum of above
            
            // Deductions
            $table->decimal('tax_deduction', 15, 2)->default(0); // PAYE
            $table->decimal('nssf_deduction', 15, 2)->default(0); // Social security
            $table->decimal('health_insurance', 15, 2)->default(0);
            $table->decimal('loan_deduction', 15, 2)->default(0);
            $table->decimal('other_deductions', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            
            // Net amount
            $table->decimal('net_salary', 15, 2)->default(0); // Gross - Deductions
            
            // Payment details
            $table->enum('payment_method', ['bank_transfer', 'cash', 'cheque', 'mobile_money'])->default('bank_transfer');
            $table->string('payment_reference')->nullable(); // Bank ref, cheque no, etc.
            $table->enum('status', ['draft', 'pending', 'approved', 'paid', 'cancelled'])->default('draft');
            
            // Additional info
            $table->integer('working_days')->nullable();
            $table->integer('days_worked')->nullable();
            $table->decimal('overtime_hours', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // For additional custom fields
            
            // Audit fields
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('paid_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['employee_id', 'period_year', 'period_month']);
            $table->index('payment_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_records');
    }
};
