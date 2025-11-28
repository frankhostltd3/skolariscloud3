<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('admission_no')->unique();
            $table->string('email')->nullable()->unique();
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('national_id')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('blood_group')->nullable();

            // Contact Information
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Kenya');

            // Academic Information
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('class_stream_id')->nullable()->constrained('class_streams')->nullOnDelete();
            $table->string('roll_number')->nullable();
            $table->string('section')->nullable();
            $table->date('admission_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'graduated', 'transferred', 'expelled'])->default('active');

            // Guardian/Parent Information
            $table->string('father_name')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_email')->nullable();

            $table->string('mother_name')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_email')->nullable();

            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_email')->nullable();

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();

            // Medical Information
            $table->text('medical_conditions')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medications')->nullable();

            // Previous School Information
            $table->string('previous_school')->nullable();
            $table->string('previous_class')->nullable();
            $table->text('transfer_reason')->nullable();

            // Additional Information
            $table->text('notes')->nullable();
            $table->boolean('has_special_needs')->default(false);
            $table->text('special_needs_description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
