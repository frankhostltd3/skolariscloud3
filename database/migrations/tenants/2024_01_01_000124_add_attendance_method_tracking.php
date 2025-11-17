<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add attendance method tracking to existing attendance tables
        if (Schema::connection('tenant')->hasTable('attendances')) {
            Schema::connection('tenant')->table('attendances', function (Blueprint $table) {
                if (!Schema::connection('tenant')->hasColumn('attendances', 'attendance_method')) {
                    $table->string('attendance_method')->default('manual')->after('status');
                    // manual, qr_code, barcode, fingerprint, optical
                }
                if (!Schema::connection('tenant')->hasColumn('attendances', 'device_id')) {
                    $table->string('device_id')->nullable()->after('attendance_method');
                }
                if (!Schema::connection('tenant')->hasColumn('attendances', 'verification_score')) {
                    $table->integer('verification_score')->nullable()->after('device_id'); // 0-100 for biometric
                }
                if (!Schema::connection('tenant')->hasColumn('attendances', 'scan_data')) {
                    $table->text('scan_data')->nullable()->after('verification_score'); // QR/Barcode data or biometric info
                }
            });
        }

        if (Schema::connection('tenant')->hasTable('staff_attendances')) {
            Schema::connection('tenant')->table('staff_attendances', function (Blueprint $table) {
                if (!Schema::connection('tenant')->hasColumn('staff_attendances', 'attendance_method')) {
                    $table->string('attendance_method')->default('manual')->after('status');
                }
                if (!Schema::connection('tenant')->hasColumn('staff_attendances', 'device_id')) {
                    $table->string('device_id')->nullable()->after('attendance_method');
                }
                if (!Schema::connection('tenant')->hasColumn('staff_attendances', 'verification_score')) {
                    $table->integer('verification_score')->nullable()->after('device_id');
                }
                if (!Schema::connection('tenant')->hasColumn('staff_attendances', 'scan_data')) {
                    $table->text('scan_data')->nullable()->after('verification_score');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::connection('tenant')->hasTable('attendances')) {
            Schema::connection('tenant')->table('attendances', function (Blueprint $table) {
                $table->dropColumn(['attendance_method', 'device_id', 'verification_score', 'scan_data']);
            });
        }

        if (Schema::connection('tenant')->hasTable('staff_attendances')) {
            Schema::connection('tenant')->table('staff_attendances', function (Blueprint $table) {
                $table->dropColumn(['attendance_method', 'device_id', 'verification_score', 'scan_data']);
            });
        }
    }
};
