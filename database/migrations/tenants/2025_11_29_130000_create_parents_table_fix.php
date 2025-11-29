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
        if (!Schema::hasTable('parents')) {
            Schema::create('parents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                
                // Personal Information
                $table->string('first_name');
                $table->string('last_name');
                $table->string('middle_name')->nullable();
                $table->string('gender')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->string('national_id')->nullable();
                $table->string('blood_group')->nullable();
                $table->string('profile_photo')->nullable();
                
                // Contact Information
                $table->string('phone')->nullable();
                $table->string('alternate_phone')->nullable();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('country')->nullable();
                
                // Occupation Information
                $table->string('occupation')->nullable();
                $table->string('employer')->nullable();
                $table->string('work_phone')->nullable();
                $table->text('work_address')->nullable();
                $table->decimal('annual_income', 15, 2)->nullable();
                
                // Relationship
                $table->json('relation_to_students')->nullable();
                
                // Emergency Contact
                $table->string('emergency_contact_name')->nullable();
                $table->string('emergency_contact_phone')->nullable();
                $table->string('emergency_contact_relation')->nullable();
                
                // Additional
                $table->text('medical_conditions')->nullable();
                $table->text('notes')->nullable();
                $table->string('status')->default('active');
                
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
