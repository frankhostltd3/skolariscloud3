<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\StaffAttendance;
use App\Models\User;
use App\Models\Academic\Enrollment;
use App\Models\Academic\ClassRoom;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AttendanceKioskController extends Controller
{
    public function index()
    {
        return view('admin.attendance.kiosk');
    }

    // Ajax helper: given identifier, return active classes list
    public function studentClasses(Request $request)
    {
        $identifier = $request->get('identifier');
        if (!$identifier) return response()->json(['classes' => []]);
        $user = User::where('student_id', $identifier)
            ->orWhere('email', $identifier)
            ->first();
        if (!$user) return response()->json(['classes' => []]);
        $classes = \App\Models\Academic\Enrollment::where('student_id', $user->id)
            ->where('status', 'active')
            ->with('class')
            ->get()
            ->map(fn($e) => ['id' => $e->class_id, 'name' => $e->class?->name, 'section' => $e->class?->section])
            ->values();
        return response()->json(['classes' => $classes]);
    }

    // Simple punch endpoint: accept user_id or code; type student|staff
    public function punch(Request $request)
    {
        // 1) Validate device token from header (unless disabled in settings)
        $requireToken = (bool) Setting::get('kiosk.require_token', true);
        if ($requireToken) {
            $deviceToken = $request->header('X-Device-Token');
            $allowedTokens = Setting::get('kiosk.allowed_tokens', []); // array of tokens
            if (!is_array($allowedTokens)) {
                $allowedTokens = $allowedTokens ? [$allowedTokens] : [];
            }
            if (empty($allowedTokens) || !$deviceToken || !in_array($deviceToken, $allowedTokens, true)) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized device'], 401);
            }
        } else {
            $deviceToken = $request->header('X-Device-Token');
        }

        $validated = $request->validate([
            'type' => 'required|in:student,staff',
            'identifier' => 'required|string', // could be student_id/staff_id/teacher_id/email
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $today = Carbon::today()->format('Y-m-d');
        // Identify user by multiple identifiers
        $user = User::where('student_id', $validated['identifier'])
            ->orWhere('teacher_id', $validated['identifier'])
            ->orWhere('staff_id', $validated['identifier'])
            ->orWhere('email', $validated['identifier'])
            ->first();

        if (!$user) {
            return response()->json(['ok' => false, 'message' => 'User not found'], 404);
        }

        // Basic rate limiting: per device and per user
        $deviceKey = 'kiosk:device:' . sha1((string) $deviceToken);
        $userKey = 'kiosk:user:' . $user->id;
        if (Cache::has($deviceKey) || Cache::has($userKey)) {
            return response()->json(['ok' => false, 'message' => 'Too many requests, please wait a moment'], 429);
        }
        // Set short TTL throttle (e.g., 5 seconds)
        Cache::put($deviceKey, 1, now()->addSeconds(5));
        Cache::put($userKey, 1, now()->addSeconds(5));

        if ($validated['type'] === 'student') {
            // Infer class from current active enrollment if class_id not provided
            $classId = $validated['class_id'] ?? null;
            if (!$classId) {
                $enrollment = Enrollment::where('student_id', $user->id)
                    ->where('status', 'active')
                    ->latest('enrollment_date')
                    ->first();
                $classId = $enrollment?->class_id;
            }
            if (!$classId) {
                return response()->json(['ok' => false, 'message' => 'No active class found for student'], 422);
            }
            Attendance::updateOrCreate([
                'student_id' => $user->id,
                'class_id' => $classId,
                'date' => $today,
            ], [
                'status' => 'present',
                'marked_by' => $user->id, // self-punch
                'marked_at' => now(),
            ]);
        } else {
            StaffAttendance::updateOrCreate([
                'user_id' => $user->id,
                'date' => $today,
            ], [
                'status' => 'present',
                'marked_by' => $user->id,
                'marked_at' => now(),
            ]);
        }

        return response()->json(['ok' => true, 'message' => 'Attendance recorded']);
    }
}