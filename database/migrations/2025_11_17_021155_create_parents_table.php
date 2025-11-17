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
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('national_id')->nullable()->unique();
            $table->string('blood_group')->nullable();
            $table->string('profile_photo')->nullable();
            
            // Contact Information
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Kenya');
            
            // Occupation Information
            $table->string('occupation')->nullable();
            $table->string('employer')->nullable();
            $table->string('work_phone')->nullable();
            $table->text('work_address')->nullable();
            $table->decimal('annual_income', 12, 2)->nullable();
            
            // Relationship to Students (JSON array of student_id => relationship)
            $table->json('relation_to_students')->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            
            // Additional Information
            $table->text('medical_conditions')->nullable();
            $table->text('notes')->nullable();
            
            // Status
            $table->enum('status', ['active', 'inactive', 'deceased'])->default('active');
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('first_name');
            $table->index('last_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
