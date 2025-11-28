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
        Schema::connection($this->centralConnection())->create('landlord_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_invoice_id')->constrained('landlord_invoices')->onDelete('cascade');
            $table->string('line_type', 32)->default('service');
            $table->string('description');
            $table->string('category')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection($this->centralConnection())->dropIfExists('landlord_invoice_items');
    }
};
