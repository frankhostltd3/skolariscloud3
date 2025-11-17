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
        if (!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('iso_code_2', 2)->unique(); // ISO 3166-1 alpha-2 (e.g., 'UG', 'KE', 'US')
                $table->string('iso_code_3', 3)->unique(); // ISO 3166-1 alpha-3 (e.g., 'UGA', 'KEN', 'USA')
                $table->string('phone_code')->nullable(); // e.g., '+256', '+254'
                $table->string('currency_code', 3)->nullable(); // ISO 4217 (e.g., 'UGX', 'KES', 'USD')
                $table->string('currency_symbol', 10)->nullable(); // e.g., 'UGX', 'KSh', '$'
                $table->string('timezone')->nullable(); // e.g., 'Africa/Kampala'
                $table->string('flag_emoji', 10)->nullable(); // Unicode flag emoji
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('iso_code_2');
                $table->index('is_active');
            });
        }

        if (!Schema::hasTable('examination_bodies')) {
            Schema::create('examination_bodies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id');
                $table->string('name'); // e.g., 'UNEB', 'Cambridge', 'KNEC'
                $table->string('code')->nullable(); // Short code
                $table->unsignedBigInteger('country_id')->nullable();
                $table->string('website')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_international')->default(false); // Cambridge, IB, etc.
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
                $table->index('school_id');
                $table->index('country_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examination_bodies');
        Schema::dropIfExists('countries');
    }
};
