<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSetting;
use App\Models\BiometricTemplate;
use App\Services\Attendance\FingerprintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeviceMonitoringController extends Controller
{
    protected $fingerprintService;

    public function __construct()
    {
        // FingerprintService will be initialized in each method with settings
    }

    /**
     * Display device monitoring dashboard
     */
    public function index()
    {
        $schoolId = auth()->user()->school_id;
        $settings = AttendanceSetting::getOrCreateForSchool($schoolId);
        $this->fingerprintService = new FingerprintService($settings);

        // Get device info from settings
        $deviceConfig = [
            'enabled' => $settings->fingerprint_enabled,
            'device_type' => $settings->fingerprint_device_type,
            'device_ip' => $settings->fingerprint_device_ip,
            'device_port' => $settings->fingerprint_device_port,
        ];

        // Get device status
        $deviceStatus = null;
        if ($deviceConfig['enabled']) {
            try {
                $status = $this->fingerprintService->getDeviceStatus(
                    $deviceConfig['device_ip'],
                    $deviceConfig['device_port']
                );
                $deviceStatus = $status;
            } catch (\Exception $e) {
                $deviceStatus = [
                    'connected' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        // Get biometric enrollment metrics (last 7 days)
        $metrics = BiometricTemplate::where('school_id', $schoolId)
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_scans'),
                DB::raw('AVG(quality_score) as avg_score'),
                DB::raw('SUM(CASE WHEN quality_score >= 70 THEN 1 ELSE 0 END) as successful_scans')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Recent low-quality templates
        $recentErrors = BiometricTemplate::where('school_id', $schoolId)
            ->where('quality_score', '<', 50)
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->limit(10)
            ->get();

        // Device activity (last 24 hours)
        $uptimeData = BiometricTemplate::where('school_id', $schoolId)
            ->whereBetween('created_at', [now()->subDay(), now()])
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as scans')
            )
            ->groupBy('hour')
            ->get();

        return view('admin.attendance.device-monitoring', compact(
            'deviceConfig',
            'deviceStatus',
            'metrics',
            'recentErrors',
            'uptimeData'
        ));
    }

    /**
     * Test device connection
     */
    public function testConnection(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $settings = AttendanceSetting::getOrCreateForSchool($schoolId);
        $this->fingerprintService = new FingerprintService($settings);

        try {
            $status = $this->fingerprintService->getDeviceStatus(
                $settings->fingerprint_device_ip,
                $settings->fingerprint_device_port
            );

            return response()->json([
                'success' => true,
                'status' => $status,
                'message' => 'Device connected successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Device connection test failed', [
                'school_id' => $schoolId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get real-time device stats
     */
    public function getStats()
    {
        $schoolId = auth()->user()->school_id;

        $stats = [
            'today_scans' => AttendanceRecord::where('school_id', $schoolId)
                ->where('attendance_method', 'fingerprint')
                ->whereDate('created_at', today())
                ->count(),

            'success_rate' => AttendanceRecord::where('school_id', $schoolId)
                ->where('attendance_method', 'fingerprint')
                ->whereDate('created_at', today())
                ->avg('verification_score'),

            'last_scan' => AttendanceRecord::where('school_id', $schoolId)
                ->where('attendance_method', 'fingerprint')
                ->latest()
                ->first(),
        ];

        return response()->json($stats);
    }
}
