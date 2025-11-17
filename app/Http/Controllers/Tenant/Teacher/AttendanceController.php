<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Academic\ClassRoom;
use App\Models\Attendance;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\BiometricService;
use App\Services\BarcodeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    protected $attendanceService;
    protected $biometricService;
    protected $barcodeService;

    public function __construct(
        AttendanceService $attendanceService,
        BiometricService $biometricService,
        BarcodeService $barcodeService
    ) {
        $this->attendanceService = $attendanceService;
        $this->biometricService = $biometricService;
        $this->barcodeService = $barcodeService;
    }

    /**
     * Attendance dashboard - overview of today's attendance
     */
    public function index()
    {
        $teacher = Auth::user();
        $today = Carbon::today();

        // Get classes where user is class teacher or subject teacher
        $classes = ClassRoom::where(function($query) use ($teacher) {
                $query->where('class_teacher_id', $teacher->id)
                    ->orWhereHas('subjects', function ($q) use ($teacher) {
                        $q->where('class_subjects.teacher_id', $teacher->id);
                    });
            })
            ->withCount(['activeEnrollments as students_count'])
            ->orderBy('grade_level')
            ->orderBy('section')
            ->orderBy('stream')
            ->get();

        // Today's attendance summary per class
        $attendanceSummary = [];
        foreach ($classes as $class) {
            $total = $class->students_count;
            $marked = Attendance::where('class_id', $class->id)
                ->whereDate('attendance_date', $today)
                ->count();
            $present = Attendance::where('class_id', $class->id)
                ->whereDate('attendance_date', $today)
                ->where('status', 'present')
                ->count();
            $absent = Attendance::where('class_id', $class->id)
                ->whereDate('attendance_date', $today)
                ->where('status', 'absent')
                ->count();
            $late = Attendance::where('class_id', $class->id)
                ->whereDate('attendance_date', $today)
                ->where('status', 'late')
                ->count();

            $attendanceSummary[$class->id] = [
                'class' => $class,
                'total' => $total,
                'marked' => $marked,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'pending' => $total - $marked,
                'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
            ];
        }

        // Recent attendance activity
        $recentActivity = Attendance::whereHas('class', function ($q) use ($teacher) {
                $q->where('class_teacher_id', $teacher->id)
                    ->orWhereHas('subjects', function ($sq) use ($teacher) {
                        $sq->where('class_subjects.teacher_id', $teacher->id);
                    });
            })
            ->with(['student', 'class'])
            ->where('marked_by', $teacher->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('tenant.teacher.attendance.index', compact('attendanceSummary', 'recentActivity', 'today'));
    }

    /**
     * Select roll call method (manual, biometric, barcode)
     */
    public function takeRollCall(Request $request)
    {
        $teacher = Auth::user();
        $classId = $request->query('class_id');

        $classes = ClassRoom::where(function($query) use ($teacher) {
                $query->where('class_teacher_id', $teacher->id)
                    ->orWhereHas('subjects', function ($q) use ($teacher) {
                        $q->where('class_subjects.teacher_id', $teacher->id);
                    });
            })
            ->with(['activeEnrollments'])
            ->withCount(['activeEnrollments as students_count'])
            ->orderBy('grade_level')
            ->orderBy('section')
            ->orderBy('stream')
            ->get();

        $selectedClass = $classId ? ClassRoom::with('activeStudents')->find($classId) : null;

        // Check if attendance already taken today
        $today = Carbon::today();
        $alreadyTaken = false;
        if ($selectedClass) {
            $alreadyTaken = Attendance::where('class_id', $selectedClass->id)
                ->whereDate('attendance_date', $today)
                ->exists();
        }

        return view('tenant.teacher.attendance.take-roll-call', compact('classes', 'selectedClass', 'alreadyTaken'));
    }

    /**
     * Manual attendance entry form
     */
    public function manual(Request $request)
    {
        $teacher = Auth::user();
        $classId = $request->query('class_id');

        if (!$classId) {
            return redirect()->route('tenant.teacher.attendance.take')->with('error', 'Please select a class.');
        }

        $class = ClassRoom::with('students')->findOrFail($classId);

        // Check permission
        if ($class->class_teacher_id !== $teacher->id && !$class->subjects()->where('class_subjects.teacher_id', $teacher->id)->exists()) {
            abort(403, 'Unauthorized to take attendance for this class.');
        }

        $today = Carbon::today();
        $students = $class->students()->orderBy('name')->get();

        // Get existing attendance for today
        $existingAttendance = Attendance::where('class_id', $classId)
            ->whereDate('attendance_date', $today)
            ->pluck('status', 'student_id')
            ->toArray();

        return view('tenant.teacher.attendance.manual', compact('class', 'students', 'existingAttendance', 'today'));
    }

    /**
     * Store manual attendance
     */
    public function store(Request $request)
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'attendance_date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*' => ['required', 'in:present,absent,late,excused'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $class = ClassRoom::findOrFail($validated['class_id']);

        // Check permission
        if ($class->class_teacher_id !== $teacher->id && !$class->subjects()->where('class_subjects.teacher_id', $teacher->id)->exists()) {
            abort(403, 'Unauthorized to take attendance for this class.');
        }

        DB::beginTransaction();
        try {
            foreach ($validated['attendance'] as $studentId => $status) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'class_id' => $validated['class_id'],
                        'attendance_date' => $validated['attendance_date'],
                    ],
                    [
                        'status' => $status,
                        'marked_by' => $teacher->id,
                        'method' => 'manual',
                        'notes' => $validated['notes'] ?? null,
                    ]
                );
            }

            DB::commit();
            return redirect()->route('tenant.teacher.attendance.index')
                ->with('success', 'Attendance marked successfully for ' . count($validated['attendance']) . ' students.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save attendance: ' . $e->getMessage());
        }
    }

    /**
     * Biometric attendance (fingerprint or iris)
     */
    public function biometric(Request $request)
    {
        $teacher = Auth::user();
        $classId = $request->query('class_id');
        $type = $request->query('type', 'fingerprint'); // fingerprint or iris

        if (!$classId) {
            return redirect()->route('tenant.teacher.attendance.take')->with('error', 'Please select a class.');
        }

        $class = ClassRoom::with('students')->findOrFail($classId);

        // Check permission
        if ($class->class_teacher_id !== $teacher->id && !$class->subjects()->where('class_subjects.teacher_id', $teacher->id)->exists()) {
            abort(403, 'Unauthorized to take attendance for this class.');
        }

        $today = Carbon::today();

        // Get existing attendance for today
        $markedStudents = Attendance::where('class_id', $classId)
            ->whereDate('attendance_date', $today)
            ->with('student')
            ->get();

        // Check biometric device status
        $deviceStatus = $this->biometricService->checkDeviceStatus($type);

        return view('tenant.teacher.attendance.biometric', compact('class', 'type', 'today', 'markedStudents', 'deviceStatus'));
    }

    /**
     * Process biometric verification (AJAX endpoint)
     */
    public function processBiometric(Request $request)
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'biometric_data' => ['required', 'string'],
            'type' => ['required', 'in:fingerprint,iris'],
            'attendance_date' => ['required', 'date'],
        ]);

        // Verify biometric data and identify student
        $result = $this->biometricService->verify($validated['biometric_data'], $validated['type']);

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 400);
        }

        $studentId = $result['student_id'];

        // Mark attendance
        try {
            $attendance = Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'class_id' => $validated['class_id'],
                    'attendance_date' => $validated['attendance_date'],
                ],
                [
                    'status' => 'present',
                    'marked_by' => $teacher->id,
                    'method' => $validated['type'],
                    'biometric_verified' => true,
                ]
            );

            $student = User::find($studentId);

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked for ' . $student->name,
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'photo' => $student->photo,
                ],
                'timestamp' => now()->format('H:i:s'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to mark attendance'], 500);
        }
    }

    /**
     * Barcode/QR code scanning interface
     */
    public function barcode(Request $request)
    {
        $teacher = Auth::user();
        $classId = $request->query('class_id');

        if (!$classId) {
            return redirect()->route('tenant.teacher.attendance.take')->with('error', 'Please select a class.');
        }

        $class = ClassRoom::with('students')->findOrFail($classId);

        // Check permission
        if ($class->class_teacher_id !== $teacher->id && !$class->subjects()->where('class_subjects.teacher_id', $teacher->id)->exists()) {
            abort(403, 'Unauthorized to take attendance for this class.');
        }

        $today = Carbon::today();

        // Get existing attendance for today
        $markedStudents = Attendance::where('class_id', $classId)
            ->whereDate('attendance_date', $today)
            ->with('student')
            ->get();

        return view('tenant.teacher.attendance.barcode', compact('class', 'today', 'markedStudents'));
    }

    /**
     * Process barcode scan (AJAX endpoint)
     */
    public function processBarcode(Request $request)
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'barcode' => ['required', 'string'],
            'attendance_date' => ['required', 'date'],
        ]);

        // Decode barcode and identify student
        $result = $this->barcodeService->decode($validated['barcode']);

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 400);
        }

        $studentId = $result['student_id'];

        // Verify student belongs to the class
        $class = ClassRoom::findOrFail($validated['class_id']);
        if (!$class->students()->where('users.id', $studentId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Student not in this class'], 400);
        }

        // Mark attendance
        try {
            $attendance = Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'class_id' => $validated['class_id'],
                    'attendance_date' => $validated['attendance_date'],
                ],
                [
                    'status' => 'present',
                    'marked_by' => $teacher->id,
                    'method' => 'barcode',
                ]
            );

            $student = User::find($studentId);

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked for ' . $student->name,
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'photo' => $student->photo,
                ],
                'timestamp' => now()->format('H:i:s'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to mark attendance'], 500);
        }
    }

    /**
     * Attendance patterns and analysis
     */
    public function patterns(Request $request)
    {
        $teacher = Auth::user();
        $classId = $request->query('class_id');
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->toDateString());

        $classes = ClassRoom::where(function($query) use ($teacher) {
                $query->where('class_teacher_id', $teacher->id)
                    ->orWhereHas('subjects', function ($q) use ($teacher) {
                        $q->where('class_subjects.teacher_id', $teacher->id);
                    });
            })
            ->orderBy('grade_level')
            ->orderBy('section')
            ->orderBy('stream')
            ->get();

        $selectedClass = $classId ? ClassRoom::find($classId) : null;

        $patterns = null;
        if ($selectedClass) {
            $patterns = $this->attendanceService->analyzePatterns(
                $selectedClass->id,
                $startDate,
                $endDate
            );
        }

        return view('tenant.teacher.attendance.patterns', compact('classes', 'selectedClass', 'patterns', 'startDate', 'endDate'));
    }

    /**
     * Generate attendance reports
     */
    public function reports(Request $request)
    {
        $teacher = Auth::user();
        $classId = $request->query('class_id');
        $reportType = $request->query('type', 'summary'); // summary, detailed, defaulters
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->toDateString());

        $classes = ClassRoom::where(function($query) use ($teacher) {
                $query->where('class_teacher_id', $teacher->id)
                    ->orWhereHas('subjects', function ($q) use ($teacher) {
                        $q->where('class_subjects.teacher_id', $teacher->id);
                    });
            })
            ->orderBy('grade_level')
            ->orderBy('section')
            ->orderBy('stream')
            ->get();

        $selectedClass = $classId ? ClassRoom::find($classId) : null;

        $reportData = null;
        if ($selectedClass) {
            $reportData = $this->attendanceService->generateReport(
                $selectedClass->id,
                $reportType,
                $startDate,
                $endDate
            );
        }

        return view('tenant.teacher.attendance.reports', compact('classes', 'selectedClass', 'reportType', 'reportData', 'startDate', 'endDate'));
    }

    /**
     * Export report as PDF or Excel
     */
    public function exportReport(Request $request)
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'type' => ['required', 'in:summary,detailed,defaulters'],
            'format' => ['required', 'in:pdf,excel'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ]);

        $class = ClassRoom::findOrFail($validated['class_id']);

        // Check permission
        if ($class->class_teacher_id !== $teacher->id && !$class->subjects()->where('class_subjects.teacher_id', $teacher->id)->exists()) {
            abort(403, 'Unauthorized');
        }

        return $this->attendanceService->exportReport(
            $validated['class_id'],
            $validated['type'],
            $validated['format'],
            $validated['start_date'],
            $validated['end_date']
        );
    }
}
