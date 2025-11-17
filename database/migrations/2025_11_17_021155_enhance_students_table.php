<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Personal Information
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('last_name');
            $table->string('national_id')->nullable()->after('dob');
            $table->string('profile_photo')->nullable()->after('national_id');
            $table->string('blood_group')->nullable()->after('profile_photo');
            
            // Contact Information
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->string('country')->default('Kenya')->after('postal_code');
            
            // Academic Information
            $table->foreignId('class_id')->nullable()->after('country')->constrained('classes')->nullOnDelete();
            $table->string('roll_number')->nullable()->after('class_id');
            $table->string('section')->nullable()->after('roll_number');
            $table->date('admission_date')->nullable()->after('section');
            $table->enum('status', ['active', 'inactive', 'graduated', 'transferred', 'expelled'])->default('active')->after('admission_date');
            
            // Guardian/Parent Information
            $table->string('father_name')->nullable()->after('status');
            $table->string('father_phone')->nullable()->after('father_name');
            $table->string('father_occupation')->nullable()->after('father_phone');
            $table->string('father_email')->nullable()->after('father_occupation');
            
            $table->string('mother_name')->nullable()->after('father_email');
            $table->string('mother_phone')->nullable()->after('mother_name');
            $table->string('mother_occupation')->nullable()->after('mother_phone');
            $table->string('mother_email')->nullable()->after('mother_occupation');
            
            $table->string('guardian_name')->nullable()->after('mother_email');
            $table->string('guardian_phone')->nullable()->after('guardian_name');
            $table->string('guardian_relation')->nullable()->after('guardian_phone');
            $table->string('guardian_email')->nullable()->after('guardian_relation');
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable()->after('guardian_email');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relation')->nullable()->after('emergency_contact_phone');
            
            // Medical Information
            $table->text('medical_conditions')->nullable()->after('emergency_contact_relation');
            $table->text('allergies')->nullable()->after('medical_conditions');
            $table->text('medications')->nullable()->after('allergies');
            
            // Previous School Information
            $table->string('previous_school')->nullable()->after('medications');
            $table->string('previous_class')->nullable()->after('previous_school');
            $table->text('transfer_reason')->nullable()->after('previous_class');
            
            // Additional Information
            $table->text('notes')->nullable()->after('transfer_reason');
            $table->boolean('has_special_needs')->default(false)->after('notes');
            $table->text('special_needs_description')->nullable()->after('has_special_needs');
            
            // Soft deletes
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn([
                'first_name', 'last_name', 'gender', 'national_id', 'profile_photo', 'blood_group',
                'phone', 'address', 'city', 'state', 'postal_code', 'country',
                'class_id', 'roll_number', 'section', 'admission_date', 'status',
                'father_name', 'father_phone', 'father_occupation', 'father_email',
                'mother_name', 'mother_phone', 'mother_occupation', 'mother_email',
                'guardian_name', 'guardian_phone', 'guardian_relation', 'guardian_email',
                'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
                'medical_conditions', 'allergies', 'medications',
                'previous_school', 'previous_class', 'transfer_reason',
                'notes', 'has_special_needs', 'special_needs_description',
                'deleted_at'
            ]);
        });
    }
};
