<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Personal Information
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('last_name');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('national_id')->nullable()->unique()->after('date_of_birth');
            $table->string('profile_photo')->nullable()->after('national_id');
            
            // Contact Information
            $table->text('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->string('country')->default('Kenya')->after('postal_code');
            
            // Professional Information
            $table->string('employee_id')->nullable()->unique()->after('country');
            $table->string('qualification')->nullable()->after('employee_id');
            $table->string('specialization')->nullable()->after('qualification');
            $table->integer('experience_years')->nullable()->after('specialization');
            $table->date('joining_date')->nullable()->after('experience_years');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'visiting'])->default('full_time')->after('joining_date');
            $table->enum('status', ['active', 'on_leave', 'resigned', 'terminated'])->default('active')->after('employment_type');
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable()->after('status');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relation')->nullable()->after('emergency_contact_phone');
            
            // Additional Information
            $table->string('blood_group')->nullable()->after('emergency_contact_relation');
            $table->text('medical_conditions')->nullable()->after('blood_group');
            $table->text('notes')->nullable()->after('medical_conditions');
            
            // Soft deletes
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'gender', 'date_of_birth', 'national_id', 'profile_photo',
                'address', 'city', 'state', 'postal_code', 'country',
                'employee_id', 'qualification', 'specialization', 'experience_years', 'joining_date', 'employment_type', 'status',
                'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
                'blood_group', 'medical_conditions', 'notes',
                'deleted_at'
            ]);
        });
    }
};
