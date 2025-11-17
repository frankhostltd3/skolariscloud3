<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Services\Attendance\FingerprintService;

class AttendanceSettingController extends Controller
{
    /**
     * Display attendance settings
     */
    public function index()
    {
        $schoolId = auth()->user()->school_id;
        $settings = AttendanceSetting::getOrCreateForSchool($schoolId);

        return view('admin.attendance.settings.index', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'grace_period_minutes' => 'required|integer|min:0|max:60',
            'allow_manual_override' => 'required|boolean',
            'require_approval_for_changes' => 'required|boolean',
        ]);

        $schoolId = auth()->user()->school_id;
        $settings = AttendanceSetting::where('school_id', $schoolId)->firstOrFail();

        $settings->update($validated);

        Cache::forget("attendance_settings_{$schoolId}");

        return redirect()->back()->with('success', 'General settings updated successfully.');
    }

    /**
     * Update student attendance methods
     */
    public function updateStudentMethods(Request $request)
    {
        $validated = $request->validate([
            'student_manual_enabled' => 'required|boolean',
            'student_qr_enabled' => 'required|boolean',
            'student_barcode_enabled' => 'required|boolean',
            'student_fingerprint_enabled' => 'required|boolean',
            'student_optical_enabled' => 'required|boolean',
        ]);

        $schoolId = auth()->user()->school_id;
        $settings = AttendanceSetting::where('school_id', $schoolId)->firstOrFail();

        $settings->update($validated);

        Cache::forget("attendance_settings_{$schoolId}");

        return redirect()->back()->with('success', 'Student attendance methods updated.');
    }

    /**
     * Update staff attendance methods
     */
    public function updateStaffMethods(Request $request)
    {
        $validated = $request->validate([
            'staff_manual_enabled' => 'required|boolean',
            'staff_qr_enabled' => 'required|boolean',
            'staff_barcode_enabled' => 'required|boolean',
            'staff_fingerprint_enabled' => 'required|boolean',
            'staff_optical_enabled' => 'required|boolean',
        ]);

        $schoolId = auth()->user()->school_id;
        $settings = AttendanceSetting::where('school_id', $schoolId)->firstOrFail();

        $settings->update($validated);

        Cache::forget("attendance_settings_{$schoolId}");

        return redirect()->back()->with('success', 'Staff attendance methods updated.');
    }

    /**
     * Update QR/Barcode settings
     */
    public function updateQrSettings(Request $request)
    {
        $validated = $request->validate([
            'qr_code_format' => 'required|in:qr,barcode',
            'qr_code_size' => 'required|integer|min:100|max:500',
            'qr_code_prefix' => 'required|string|max:10',
            'qr_auto_generate' => 'required|boolean',
        ]);

        $schoolId = auth()->user()->school_id;
        $settings = AttendanceSetting::where('school_id', $schoolId)->firstOrFail();

        $settings->update($validated);

        Cache::forget("attendance_settings_{$schoolId}");

        return redirect()->back()->with('success', 'QR/Barcode settings updated.');
    }

    /**
     * Update fingerprint device settings
     */
    public function updateFingerprintSettings(Request $request)
    {
        $validated = $request->validate([
            'fingerprint_device_type' => 'required|in:zkteco,morpho,suprema,generic',
            'fingerprint_device_ip' => 'required|ip',
            'fingerprint_device_port' => 'required|integer|min:1|max:65535',
            'fingerprint_quality_threshold' => 'required|integer|min:0|max:100',
            'fingerprint_timeout_seconds' => 'required|integer|min:5|max:60',
        ]);

        $schoolId = auth()->user()->school_id;
        $settings = AttendanceSetting::where('school_id', $schoolId)->firstOrFail();

        $settings->update($validated);

        Cache::forget("attendance_settings_{$schoolId}");

        return redirect()->back()->with('success', 'Fingerprint device settings updated.');
    }

    /**
     * Test fingerprint device connection
     */
    public function testFingerprintDevice(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $settings = AttendanceSetting::where('school_id', $schoolId)->firstOrFail();

        $service = new FingerprintService();

        $result = $service->connect([
            'type' => $settings->fingerprint_device_type,
            'ip' => $settings->fingerprint_device_ip,
            'port' => $settings->fingerprint_device_port,
        ]);

        if ($result['success']) {
            // Get device status
            $status = $service->getDeviceStatus();

            return response()->json([
                'success' => true,
                'message' => 'Device connected successfully.',
                'device' => $status,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to connect to device.',
        ]);
    }

    /**
     * Update optical scanner settings
     */
    public function updateOpticalSettings(Request $request)
    {
        $validated = $request->validate([
            'optical_use_omr' => 'required|boolean',
            'optical_template_id' => 'nullable|integer',
            'optical_mark_sensitivity' => 'required|integer|min:1|max:100',
        ]);

        $schoolId = auth()->user()->school_id;
        $settings = AttendanceSetting::where('school_id', $schoolId)->firstOrFail();

        $settings->update($validated);

        Cache::forget("attendance_settings_{$schoolId}");

        return redirect()->back()->with('success', 'Optical scanner settings updated.');
    }

    /**
     * Clear all attendance settings cache
     */
    public function clearCache()
    {
        $schoolId = auth()->user()->school_id;
        Cache::forget("attendance_settings_{$schoolId}");

        return redirect()->back()->with('success', 'Attendance settings cache cleared.');
    }
}
