<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSetting;
use App\Services\Attendance\BarcodeService;
use App\Services\Attendance\AttendanceRecordingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QrScannerController extends Controller
{
    protected $barcodeService;
    protected $recordingService;

    public function __construct()
    {
        $this->barcodeService = new BarcodeService();
        $this->recordingService = new AttendanceRecordingService(auth()->user()->school_id ?? 1);
    }

    /**
     * Display QR scanner interface
     */
    public function index()
    {
        $schoolId = auth()->user()->school_id;
        $settings = AttendanceSetting::getOrCreateForSchool($schoolId);

        // Get active attendance sessions
        $sessions = Attendance::where('school_id', $schoolId)
            ->whereDate('attendance_date', today())
            ->with('class', 'subject')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.attendance.qr-scanner', compact('settings', 'sessions'));
    }

    /**
     * Process scanned QR code
     */
    public function scan(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'attendance_id' => 'nullable|integer|exists:attendance,id',
            'user_type' => 'required|in:student,staff',
        ]);

        try {
            // Parse QR code
            $parsed = $this->barcodeService->parseCode($validated['code']);

            if (!$parsed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code format.',
                    'type' => 'error',
                ], 400);
            }

            // If attendance_id provided, record for specific session
            if ($validated['attendance_id']) {
                $result = $this->recordingService->record('qr', [
                    'user_type' => $validated['user_type'],
                    'attendance_id' => $validated['attendance_id'],
                    'code' => $validated['code'],
                    'status' => 'present',
                ]);

                return response()->json($result);
            }

            // Otherwise, just validate the code
            return response()->json([
                'success' => true,
                'message' => 'QR code validated successfully.',
                'user' => $parsed,
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing QR code: ' . $e->getMessage(),
                'type' => 'error',
            ], 500);
        }
    }

    /**
     * Get user details from QR code
     */
    public function getUserInfo(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
        ]);

        $parsed = $this->barcodeService->parseCode($validated['code']);

        if (!$parsed) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code.',
            ], 400);
        }

        // Fetch user details based on type
        if ($parsed['user_type'] === 'STU') {
            $user = \App\Models\Student::find($parsed['user_id']);
            $userType = 'student';
        } else {
            $user = \App\Models\Employee::find($parsed['user_id']);
            $userType = 'staff';
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name ?? $user->full_name,
                'type' => $userType,
                'photo' => $user->photo_url ?? null,
                'class' => $user->class->name ?? null,
            ],
        ]);
    }
}
