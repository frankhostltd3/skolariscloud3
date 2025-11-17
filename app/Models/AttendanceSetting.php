<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSetting extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'student_manual_enabled',
        'student_qr_enabled',
        'student_barcode_enabled',
        'student_fingerprint_enabled',
        'student_optical_enabled',
        'staff_manual_enabled',
        'staff_qr_enabled',
        'staff_barcode_enabled',
        'staff_fingerprint_enabled',
        'staff_optical_enabled',
        'qr_code_format',
        'qr_code_size',
        'qr_code_prefix',
        'auto_generate_codes',
        'fingerprint_device_type',
        'fingerprint_device_ip',
        'fingerprint_device_port',
        'fingerprint_device_config',
        'fingerprint_timeout',
        'fingerprint_threshold',
        'optical_enable_omr',
        'optical_sheet_template',
        'optical_detection_sensitivity',
        'optical_auto_process',
        'attendance_grace_period',
        'allow_manual_override',
        'require_approval',
        'notification_settings',
    ];

    protected $casts = [
        'student_manual_enabled' => 'boolean',
        'student_qr_enabled' => 'boolean',
        'student_barcode_enabled' => 'boolean',
        'student_fingerprint_enabled' => 'boolean',
        'student_optical_enabled' => 'boolean',
        'staff_manual_enabled' => 'boolean',
        'staff_qr_enabled' => 'boolean',
        'staff_barcode_enabled' => 'boolean',
        'staff_fingerprint_enabled' => 'boolean',
        'staff_optical_enabled' => 'boolean',
        'auto_generate_codes' => 'boolean',
        'optical_enable_omr' => 'boolean',
        'optical_auto_process' => 'boolean',
        'allow_manual_override' => 'boolean',
        'require_approval' => 'boolean',
        'notification_settings' => 'array',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get enabled methods for a user type (student or staff)
     */
    public function getEnabledMethods(string $userType = 'student'): array
    {
        $prefix = $userType === 'staff' ? 'staff' : 'student';
        $methods = [];

        if ($this->{$prefix . '_manual_enabled'}) {
            $methods[] = 'manual';
        }
        if ($this->{$prefix . '_qr_enabled'}) {
            $methods[] = 'qr_code';
        }
        if ($this->{$prefix . '_barcode_enabled'}) {
            $methods[] = 'barcode';
        }
        if ($this->{$prefix . '_fingerprint_enabled'}) {
            $methods[] = 'fingerprint';
        }
        if ($this->{$prefix . '_optical_enabled'}) {
            $methods[] = 'optical';
        }

        return $methods;
    }

    /**
     * Check if a method is enabled for a user type
     */
    public function isMethodEnabled(string $method, string $userType = 'student'): bool
    {
        $prefix = $userType === 'staff' ? 'staff' : 'student';
        $field = $prefix . '_' . str_replace('_code', '', $method) . '_enabled';

        return $this->$field ?? false;
    }

    /**
     * Get the default settings for a school
     */
    public static function getOrCreateForSchool(int $schoolId): self
    {
        return static::firstOrCreate(
            ['school_id' => $schoolId],
            [
                'student_manual_enabled' => true,
                'staff_manual_enabled' => true,
                'qr_code_format' => 'qr',
                'qr_code_size' => 200,
                'auto_generate_codes' => true,
                'fingerprint_timeout' => 30,
                'fingerprint_threshold' => 80,
                'optical_detection_sensitivity' => 70,
                'attendance_grace_period' => 15,
                'allow_manual_override' => true,
                'require_approval' => false,
            ]
        );
    }
}
