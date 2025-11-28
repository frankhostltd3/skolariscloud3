<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function centralConnection(): string
    {
        return config(
            'tenancy.database.central_connection',
            config('database.central_connection', config('database.default'))
        );
    }

    public function up(): void
    {
        Schema::connection($this->centralConnection())->create('landlord_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->string('tenant_id', 64)->nullable()->index();
            $table->string('tenant_name_snapshot')->nullable();
            $table->string('tenant_plan_snapshot')->nullable();
            $table->string('status', 32)->default('draft')->index();
            $table->boolean('auto_generated')->default(false);
            $table->date('issued_at')->nullable();
            $table->date('due_at')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('balance_due', 12, 2)->default(0);
            $table->integer('warning_level')->default(0);
            $table->timestamp('last_warning_sent_at')->nullable();
            $table->timestamp('suspension_at')->nullable();
            $table->timestamp('termination_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::connection($this->centralConnection())->dropIfExists('landlord_invoices');
    }
};
