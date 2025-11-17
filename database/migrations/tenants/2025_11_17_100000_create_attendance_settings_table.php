<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('tenant')->create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');

            // Student Attendance Methods
            $table->boolean('student_manual_enabled')->default(true);
            $table->boolean('student_qr_enabled')->default(false);
            $table->boolean('student_barcode_enabled')->default(false);
            $table->boolean('student_fingerprint_enabled')->default(false);
            $table->boolean('student_optical_enabled')->default(false);

            // Staff Attendance Methods
            $table->boolean('staff_manual_enabled')->default(true);
            $table->boolean('staff_qr_enabled')->default(false);
            $table->boolean('staff_barcode_enabled')->default(false);
            $table->boolean('staff_fingerprint_enabled')->default(false);
            $table->boolean('staff_optical_enabled')->default(false);

            // QR/Barcode Settings
            $table->string('qr_code_format')->default('qr'); // qr, code128, code39, etc.
            $table->integer('qr_code_size')->default(200); // pixels
            $table->string('qr_code_prefix')->nullable(); // School prefix for codes
            $table->boolean('auto_generate_codes')->default(true);

            // Fingerprint Settings
            $table->string('fingerprint_device_type')->nullable(); // zkteco, morpho, suprema, etc.
            $table->string('fingerprint_device_ip')->nullable();
            $table->integer('fingerprint_device_port')->nullable();
            $table->text('fingerprint_device_config')->nullable(); // JSON config
            $table->integer('fingerprint_timeout')->default(30); // seconds
            $table->integer('fingerprint_threshold')->default(80); // matching threshold 0-100

            // Optical Scanner Settings
            $table->boolean('optical_enable_omr')->default(false);
            $table->string('optical_sheet_template')->nullable(); // template file path
            $table->integer('optical_detection_sensitivity')->default(70); // 0-100
            $table->boolean('optical_auto_process')->default(false);

            // General Settings
            $table->integer('attendance_grace_period')->default(15); // minutes
            $table->boolean('allow_manual_override')->default(true);
            $table->boolean('require_approval')->default(false);
            $table->json('notification_settings')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->index('school_id');
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('attendance_settings');
    }
};
