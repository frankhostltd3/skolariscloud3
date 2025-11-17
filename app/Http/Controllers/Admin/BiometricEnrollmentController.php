<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BiometricTemplate;
use App\Models\User;
use App\Models\AttendanceSetting;
use App\Services\Attendance\FingerprintService;
use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BiometricEnrollmentController extends Controller
{
    protected $fingerprintService;

    public function __construct()
    {
        // FingerprintService will be initialized in each method with settings
    }

    /**
     * Display enrollment interface
     */
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $settings = AttendanceSetting::getOrCreateForSchool($schoolId);
        $this->fingerprintService = new FingerprintService($settings);

        $userType = $request->get('type', 'student');

        // Get users based on type
        $users = User::where('school_id', $schoolId)
            ->where('user_type', $userType)
            ->withCount('biometricTemplates')
            ->orderBy('name')
            ->paginate(50);

        return view('admin.attendance.biometric-enrollment', compact('settings', 'users', 'userType'));
    }

    /**
     * Show enrollment form for specific user
     */
    public function enroll($userType, $userId)
    {
        $schoolId = auth()->user()->school_id;
        $settings = AttendanceSetting::getOrCreateForSchool($schoolId);

        $user = User::where('school_id', $schoolId)
            ->where('id', $userId)
            ->where('user_type', $userType)
            ->firstOrFail();

        // Get existing templates
        $templates = BiometricTemplate::where('school_id', $schoolId)
            ->where('user_id', $userId)
            ->where('user_type', $userType)
            ->get();

        return view('admin.attendance.biometric-enroll-form', compact('settings', 'user', 'userType', 'templates'));
    }

    /**
     * Capture and save fingerprint
     */
    public function capture(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'user_type' => 'required|in:student,staff',
            'finger_position' => 'required|integer|min:1|max:10',
            'template_data' => 'required|string',
            'quality_score' => 'required|integer|min:0|max:100',
        ]);

        $schoolId = auth()->user()->school_id;
        $settings = AttendanceSetting::getOrCreateForSchool($schoolId);

        // Check quality threshold
        if ($validated['quality_score'] < $settings->fingerprint_quality_threshold) {
            return response()->json([
                'success' => false,
                'message' => "Quality too low ({$validated['quality_score']}%). Minimum required: {$settings->fingerprint_quality_threshold}%",
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Delete existing template for same finger
            BiometricTemplate::where('school_id', $schoolId)
                ->where('user_id', $validated['user_id'])
                ->where('user_type', $validated['user_type'])
                ->where('finger_position', $validated['finger_position'])
                ->delete();

            // Create new template
            $template = BiometricTemplate::create([
                'school_id' => $schoolId,
                'user_id' => $validated['user_id'],
                'user_type' => $validated['user_type'],
                'template_data' => encrypt($validated['template_data']),
                'finger_position' => $validated['finger_position'],
                'quality_score' => $validated['quality_score'],
                'device_id' => $settings->fingerprint_device_ip,
                'enrolled_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fingerprint enrolled successfully!',
                'template' => [
                    'id' => $template->id,
                    'finger_name' => $template->getFingerName(),
                    'quality_score' => $template->quality_score,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Enrollment failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete biometric template
     */
    public function delete($templateId)
    {
        $template = BiometricTemplate::findOrFail($templateId);

        // Verify ownership
        if ($template->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        $template->delete();

        return redirect()->back()->with('success', 'Fingerprint template deleted.');
    }

    /**
     * Test device connection
     */
    public function testDevice()
    {
        $schoolId = auth()->user()->school_id;
        $settings = AttendanceSetting::getOrCreateForSchool($schoolId);
        $this->fingerprintService = new FingerprintService($settings);

        $result = $this->fingerprintService->connect([
            'type' => $settings->fingerprint_device_type,
            'ip' => $settings->fingerprint_device_ip,
            'port' => $settings->fingerprint_device_port,
        ]);

        if ($result['success']) {
            $status = $this->fingerprintService->getDeviceStatus();
            return response()->json([
                'success' => true,
                'message' => 'Device connected successfully!',
                'device' => $status,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Connection failed',
        ]);
    }
}
