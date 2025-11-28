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
        if (Schema::hasTable('currencies')) {
            return;
        }

        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // USD, EUR, etc.
            $table->string('name'); // United States Dollar
            $table->string('symbol', 10)->nullable(); // $, €, etc.
            $table->tinyInteger('decimals')->default(2); // Number of decimal places
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->index(['code', 'enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
