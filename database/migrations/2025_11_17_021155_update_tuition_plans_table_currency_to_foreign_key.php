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
        Schema::table('tuition_plans', function (Blueprint $table) {
            // Drop the old currency string column
            $table->dropColumn('currency');

            // Add the new currency_id foreign key column
            $table->unsignedBigInteger('currency_id')->nullable()->after('total_amount');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tuition_plans', function (Blueprint $table) {
            // Drop the foreign key and column
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');

            // Restore the old currency string column
            $table->string('currency', 3)->default('USD')->after('total_amount');
        });
    }
};
