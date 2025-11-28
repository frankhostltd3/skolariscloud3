<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Academic\ClassRoom;
use App\Models\Attendance;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\Attendance\FingerprintService;
use App\Services\Attendance\BarcodeService;
use App\Models\AttendanceSetting;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    protected $attendanceService;
    protected $barcodeService;

    public function __construct(
        AttendanceService $attendanceService,
        BarcodeService $barcodeService
    ) {
        $this->attendanceService = $attendanceService;
        $this->barcodeService = $barcodeService;
    }

    /**
     * Attendance dashboard - overview of today's attendance
     */
    public function index()
    {
        $teacher = Auth::user();
        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $schema = Schema::connection($connection);
        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);
        $classSubjectTable = $this->resolveClassSubjectTable($connection);
        $hasEnrollments = $schema->hasTable('enrollments');
        $hasStudentTable = $schema->hasTable('students');
        $hasMarkedByColumn = tenant_column_exists('attendance', 'marked_by', $connection);
        $today = Carbon::today();

        $classesQuery = $this->teacherClassesQuery($teacher->id, $classSubjectTable, $hasClassTeacherColumn);

        if ($hasEnrollments) {
            $classesQuery->withCount(['students as students_count' => function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('enrollments.status', 'active')
                        ->orWhereNull('enrollments.status');
                });
            }]);
        } elseif ($hasStudentTable) {
            $classesQuery->withCount(['students as students_count']);
        }

        $this->applyClassOrdering($classesQuery, $connection);

        $classes = $classesQuery->get();

        $classes->each(function ($class) use ($hasEnrollments, $hasStudentTable) {
            if ($class->getAttribute('students_count') === null) {
                if ($hasEnrollments || $hasStudentTable) {
                    $class->setAttribute('students_count', $class->students()->count());
                } else {
                    $class->setAttribute('students_count', 0);
                }
            }
        });

        if (! $hasEnrollments && ! $hasStudentTable) {
            $classes->each(function ($class) {
                $class->setAttribute('students_count', 0);
            });
        }

        // Today's attendance summary per class
        $attendanceSummary = [];
        foreach ($classes as $class) {
            $total = $class->students_count;
            
            $attendance = Attendance::where('class_id', $class->id)
                ->whereDate('attendance_date', $today)
                ->first();

            if ($attendance) {
                $stats = $attendance->getStatistics();
                $marked = $stats['total'];
                $present = $stats['present'];
                $absent = $stats['absent'];
                $late = $stats['late'];
            } else {
                $marked = 0;
                $present = 0;
                $absent = 0;
                $late = 0;
            }

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
        $recentActivityQuery = Attendance::whereHas('class', function ($q) use ($teacher, $classSubjectTable, $hasClassTeacherColumn) {
                $this->applyTeacherClassConstraint($q, $teacher->id, $classSubjectTable, $hasClassTeacherColumn);
            })
            ->with(['class', 'teacher']);

        if ($hasMarkedByColumn) {
            // If marked_by exists, use it, otherwise fallback to teacher_id
             $recentActivityQuery->where(function($q) use ($teacher) {
                 $q->where('teacher_id', $teacher->id);
             });
        } else {
             $recentActivityQuery->where('teacher_id', $teacher->id);
        }

        $recentActivity = $recentActivityQuery
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

        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $schema = Schema::connection($connection);
        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);
        $classSubjectTable = $this->resolveClassSubjectTable($connection);
        $hasEnrollments = $schema->hasTable('enrollments');
        $hasStudentTable = $schema->hasTable('students');

        $classesQuery = $this->teacherClassesQuery($teacher->id, $classSubjectTable, $hasClassTeacherColumn);

        if ($hasEnrollments) {
            $classesQuery->with(['students' => function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('enrollments.status', 'active')
                        ->orWhereNull('enrollments.status');
                });
            }]);
            $classesQuery->withCount(['students as students_count' => function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('enrollments.status', 'active')
                        ->orWhereNull('enrollments.status');
                });
            }]);
        } elseif ($hasStudentTable) {
            $classesQuery->with('students');
            $classesQuery->withCount(['students as students_count']);
        }

        $this->applyClassOrdering($classesQuery, $connection);

        $classes = $classesQuery->get();

        $classes->each(function ($class) use ($hasEnrollments, $hasStudentTable) {
            if ($class->getAttribute('students_count') === null) {
                if ($hasEnrollments || $hasStudentTable) {
                    $class->setAttribute('students_count', $class->students()->count());
                } else {
                    $class->setAttribute('students_count', 0);
                }
            }
        });

        $selectedClass = null;
        if ($classId) {
            $selectedClass = ClassRoom::find($classId);
            if ($selectedClass) {
                if ($hasEnrollments) {
                    $selectedClass->load(['students' => function ($query) {
                        $query->where(function ($subQuery) {
                            $subQuery->where('enrollments.status', 'active')
                                ->orWhereNull('enrollments.status');
                        });
                    }]);
                } elseif ($hasStudentTable) {
                    $selectedClass->load('students');
                }
            }
        }

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
        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $schema = Schema::connection($connection);
        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);
        $classSubjectTable = $this->resolveClassSubjectTable($connection);
        $hasEnrollments = $schema->hasTable('enrollments');
        $hasStudentTable = $schema->hasTable('students');

        if (!$classId) {
            return redirect()->route('tenant.teacher.attendance.take')->with('error', 'Please select a class.');
        }

        $class = ClassRoom::findOrFail($classId);

        if ($hasEnrollments) {
            $class->load(['students' => function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('enrollments.status', 'active')
                        ->orWhereNull('enrollments.status');
                });
            }]);
        } elseif ($hasStudentTable) {
            $class->load('students');
        }

        // Check permission
        if (! $this->teacherHasClassSubjectAccess($class, $teacher->id, $classSubjectTable, $hasClassTeacherColumn)) {
            abort(403, 'Unauthorized to take attendance for this class.');
        }

        $today = Carbon::today();
        $studentsQuery = $class->students();
        if ($hasEnrollments) {
            $studentsQuery->where(function ($subQuery) {
                $subQuery->where('enrollments.status', 'active')
                    ->orWhereNull('enrollments.status');
            });
        }
        $students = $studentsQuery->orderBy('name')->get();

        // Get existing attendance for today
        $attendance = Attendance::where('class_id', $classId)
            ->whereDate('attendance_date', $today)
            ->first();

        $existingAttendance = [];
        if ($attendance) {
            $existingAttendance = $attendance->records()->pluck('status', 'student_id')->toArray();
        }

        return view('tenant.teacher.attendance.manual', compact('class', 'students', 'existingAttendance', 'today'));
    }

    /**
     * Store manual attendance
     */
    public function store(Request $request)
    {
        $teacher = Auth::user();
        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $schema = Schema::connection($connection);
        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);
        $classSubjectTable = $this->resolveClassSubjectTable($connection);
        $hasEnrollments = $schema->hasTable('enrollments');
        $hasMarkedByColumn = tenant_column_exists('attendance', 'marked_by', $connection);
        $hasMethodColumn = tenant_column_exists('attendance', 'method', $connection);
        $hasNotesColumn = tenant_column_exists('attendance', 'notes', $connection);

        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'attendance_date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*' => ['required', 'in:present,absent,late,excused'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $class = ClassRoom::findOrFail($validated['class_id']);

        // Check permission
        if (! $this->teacherHasClassSubjectAccess($class, $teacher->id, $classSubjectTable, $hasClassTeacherColumn)) {
            abort(403, 'Unauthorized to take attendance for this class.');
        }

        DB::beginTransaction();
        try {
            // 1. Create or update the parent Attendance record
            $attendanceData = [
                'school_id' => $teacher->school_id,
                'class_id' => $validated['class_id'],
                'teacher_id' => $teacher->id,
                'attendance_date' => $validated['attendance_date'],
                'attendance_type' => 'classroom',
            ];

            if ($hasNotesColumn && isset($validated['notes'])) {
                $attendanceData['notes'] = $validated['notes'];
            }

            // Use firstOrCreate to avoid duplicates for the same class/date
            $attendance = Attendance::firstOrCreate(
                [
                    'class_id' => $validated['class_id'],
                    'attendance_date' => $validated['attendance_date'],
                ],
                $attendanceData
            );

            // Update notes if provided and record already existed
            if ($hasNotesColumn && isset($validated['notes'])) {
                $attendance->update(['notes' => $validated['notes']]);
            }

            foreach ($validated['attendance'] as $studentId => $status) {
                if ($hasEnrollments && ! $class->students()->where('users.id', $studentId)->exists()) {
                    continue; // skip students not in class under current schema
                }
                
                $recordData = ['status' => $status];
                // Note: marked_by, method are typically on the parent attendance record or specific log table
                // But if the schema has them on records, we can add them. 
                // Based on migration 2024_01_01_000121, attendance_records only has status and remarks.
                // Migration 2024_01_01_000124 adds method to 'attendances' (plural) table, which maps to Attendance model.
                
                // If we need to track method per student, we might need to check if columns exist on attendance_records
                // But for now, let's stick to the basic status.
                
                \App\Models\AttendanceRecord::updateOrCreate(
                    [
                        'attendance_id' => $attendance->id,
                        'student_id' => $studentId,
                    ],
                    $recordData
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
        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $schema = Schema::connection($connection);
        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);
        $classSubjectTable = $this->resolveClassSubjectTable($connection);
        $hasEnrollments = $schema->hasTable('enrollments');
        $hasStudentTable = $schema->hasTable('students');

        if (!$classId) {
            return redirect()->route('tenant.teacher.attendance.take')->with('error', 'Please select a class.');
        }

        $class = ClassRoom::findOrFail($classId);

        if ($hasEnrollments) {
            $class->load(['students' => function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('enrollments.status', 'active')
                        ->orWhereNull('enrollments.status');
                });
            }]);
        } elseif ($hasStudentTable) {
            $class->load('students');
        }

        // Check permission
        if (! $this->teacherHasClassSubjectAccess($class, $teacher->id, $classSubjectTable, $hasClassTeacherColumn)) {
            abort(403, 'Unauthorized to take attendance for this class.');
        }

        $today = Carbon::today();

        // Get existing attendance for today
        $markedStudents = Attendance::where('class_id', $classId)
            ->whereDate('attendance_date', $today)
            ->with('student')
            ->get();

        // Check biometric device status
        $settings = AttendanceSetting::getOrCreateForSchool($teacher->school_id);
        $fingerprintService = new FingerprintService($settings);
        $deviceStatus = $fingerprintService->getDeviceStatus();

        return view('tenant.teacher.attendance.biometric', compact('class', 'type', 'today', 'markedStudents', 'deviceStatus'));
    }

    /**
     * Process biometric verification (AJAX endpoint)
     */
    public function processBiometric(Request $request)
    {
        $teacher = Auth::user();
        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $hasMarkedByColumn = tenant_column_exists('attendance', 'marked_by', $connection);
        $hasMethodColumn = tenant_column_exists('attendance', 'method', $connection);
        $hasBiometricColumn = tenant_column_exists('attendance', 'biometric_verified', $connection);

        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'biometric_data' => ['required', 'string'],
            'type' => ['required', 'in:fingerprint,iris'],
            'attendance_date' => ['required', 'date'],
        ]);

        // Verify biometric data and identify student
        $settings = AttendanceSetting::getOrCreateForSchool($teacher->school_id);
        $fingerprintService = new FingerprintService($settings);
        $template = $fingerprintService->identify($validated['biometric_data']);

        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Fingerprint not recognized.'], 400);
        }

        $studentId = $template->user_id;

        // Mark attendance
        try {
            // 1. Create or update parent Attendance
            $attendanceData = [
                'school_id' => $teacher->school_id,
                'class_id' => $validated['class_id'],
                'teacher_id' => $teacher->id,
                'attendance_date' => $validated['attendance_date'],
                'attendance_type' => 'classroom',
            ];

            if ($hasMethodColumn) {
                $attendanceData['attendance_method'] = $validated['type']; // Note: migration used 'attendance_method'
            }

            $attendance = Attendance::firstOrCreate(
                [
                    'class_id' => $validated['class_id'],
                    'attendance_date' => $validated['attendance_date'],
                ],
                $attendanceData
            );

            // 2. Create or update AttendanceRecord
            $recordData = ['status' => 'present'];
            
            \App\Models\AttendanceRecord::updateOrCreate(
                [
                    'attendance_id' => $attendance->id,
                    'student_id' => $studentId,
                ],
                $recordData
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
            return response()->json(['success' => false, 'message' => 'Failed to mark attendance: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Barcode/QR code scanning interface
     */
    public function barcode(Request $request)
    {
        $teacher = Auth::user();
        $classId = $request->query('class_id');
        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $schema = Schema::connection($connection);
        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);
        $classSubjectTable = $this->resolveClassSubjectTable($connection);
        $hasEnrollments = $schema->hasTable('enrollments');
        $hasStudentTable = $schema->hasTable('students');

        if (!$classId) {
            return redirect()->route('tenant.teacher.attendance.take')->with('error', 'Please select a class.');
        }

        $class = ClassRoom::findOrFail($classId);

        if ($hasEnrollments) {
            $class->load(['students' => function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('enrollments.status', 'active')
                        ->orWhereNull('enrollments.status');
                });
            }]);
        } elseif ($hasStudentTable) {
            $class->load('students');
        }

        // Check permission
        if (! $this->teacherHasClassSubjectAccess($class, $teacher->id, $classSubjectTable, $hasClassTeacherColumn)) {
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
        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $hasMarkedByColumn = tenant_column_exists('attendance', 'marked_by', $connection);
        $hasMethodColumn = tenant_column_exists('attendance', 'method', $connection);

        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'barcode' => ['required', 'string'],
            'attendance_date' => ['required', 'date'],
        ]);

        // Decode barcode and identify student
        $parsed = $this->barcodeService->parseCode($validated['barcode']);

        if (!$parsed) {
            return response()->json(['success' => false, 'message' => 'Invalid barcode format'], 400);
        }

        $studentId = $parsed['user_id'];

        // Verify student belongs to the class
        $class = ClassRoom::findOrFail($validated['class_id']);
        if (!$class->students()->where('users.id', $studentId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Student not in this class'], 400);
        }

        // Mark attendance
        try {
            // 1. Create or update parent Attendance
            $attendanceData = [
                'school_id' => $teacher->school_id,
                'class_id' => $validated['class_id'],
                'teacher_id' => $teacher->id,
                'attendance_date' => $validated['attendance_date'],
                'attendance_type' => 'classroom',
            ];

            if ($hasMethodColumn) {
                $attendanceData['attendance_method'] = 'barcode';
            }

            $attendance = Attendance::firstOrCreate(
                [
                    'class_id' => $validated['class_id'],
                    'attendance_date' => $validated['attendance_date'],
                ],
                $attendanceData
            );

            // 2. Create or update AttendanceRecord
            $recordData = ['status' => 'present'];
            
            \App\Models\AttendanceRecord::updateOrCreate(
                [
                    'attendance_id' => $attendance->id,
                    'student_id' => $studentId,
                ],
                $recordData
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
            return response()->json(['success' => false, 'message' => 'Failed to mark attendance: ' . $e->getMessage()], 500);
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

        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);
        $classSubjectTable = $this->resolveClassSubjectTable($connection);

        $classesQuery = $this->teacherClassesQuery($teacher->id, $classSubjectTable, $hasClassTeacherColumn);
        $this->applyClassOrdering($classesQuery, $connection);
        $classes = $classesQuery->get();

        $selectedClass = $classId ? ClassRoom::find($classId) : null;
        if ($selectedClass && ! $this->teacherHasClassSubjectAccess($selectedClass, $teacher->id, $classSubjectTable, $hasClassTeacherColumn)) {
            $selectedClass = null;
        }

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

        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);
        $classSubjectTable = $this->resolveClassSubjectTable($connection);

        $classesQuery = $this->teacherClassesQuery($teacher->id, $classSubjectTable, $hasClassTeacherColumn);
        $this->applyClassOrdering($classesQuery, $connection);
        $classes = $classesQuery->get();

        $selectedClass = $classId ? ClassRoom::find($classId) : null;
        if ($selectedClass && ! $this->teacherHasClassSubjectAccess($selectedClass, $teacher->id, $classSubjectTable, $hasClassTeacherColumn)) {
            $selectedClass = null;
        }

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
        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);
        $classSubjectTable = $this->resolveClassSubjectTable($connection);

        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'type' => ['required', 'in:summary,detailed,defaulters'],
            'format' => ['required', 'in:pdf,excel'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ]);

        $class = ClassRoom::findOrFail($validated['class_id']);

        // Check permission
        if (! $this->teacherHasClassSubjectAccess($class, $teacher->id, $classSubjectTable, $hasClassTeacherColumn)) {
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

    private function resolveClassSubjectTable(string $connection): ?string
    {
        foreach (['class_subjects', 'class_subject'] as $candidate) {
            if (tenant_table_exists($candidate, $connection)) {
                return $candidate;
            }
        }

        return null;
    }

    private function teacherClassesQuery(int $teacherId, ?string $classSubjectTable, bool $hasClassTeacherColumn)
    {
        $query = ClassRoom::query();

        $this->applyTeacherClassConstraint($query, $teacherId, $classSubjectTable, $hasClassTeacherColumn);

        return $query;
    }

    private function applyTeacherClassConstraint($query, int $teacherId, ?string $classSubjectTable, bool $hasClassTeacherColumn): void
    {
        $query->where(function ($builder) use ($teacherId, $classSubjectTable, $hasClassTeacherColumn) {
            $applied = false;

            if ($hasClassTeacherColumn) {
                $builder->where('class_teacher_id', $teacherId);
                $applied = true;
            }

            if ($classSubjectTable) {
                $constraint = function ($subjectQuery) use ($teacherId, $classSubjectTable) {
                    $subjectQuery->where($classSubjectTable . '.teacher_id', $teacherId);
                };

                if ($applied) {
                    $builder->orWhereHas('subjects', $constraint);
                } else {
                    $builder->whereHas('subjects', $constraint);
                }

                $applied = true;
            }

            if (! $applied) {
                $builder->whereRaw('0 = 1');
            }
        });
    }

    private function applyClassOrdering($query, string $connection): void
    {
        if (tenant_column_exists('classes', 'grade_level', $connection)) {
            $query->orderBy('grade_level');
        }

        if (tenant_column_exists('classes', 'section', $connection)) {
            $query->orderBy('section');
        }

        if (tenant_column_exists('classes', 'stream', $connection)) {
            $query->orderBy('stream');
        }

        $query->orderBy('name');
    }

    private function teacherHasClassSubjectAccess(ClassRoom $class, int $teacherId, ?string $classSubjectTable, bool $hasClassTeacherColumn, ?int $subjectId = null): bool
    {
        $isClassTeacher = $hasClassTeacherColumn && $class->class_teacher_id === $teacherId;

        if ($isClassTeacher) {
            return true;
        }

        if (! $classSubjectTable) {
            return false;
        }

        $subjectQuery = $class->subjects()->where($classSubjectTable . '.teacher_id', $teacherId);

        if ($subjectId) {
            $subjectQuery->where('subjects.id', $subjectId);
        }

        return $subjectQuery->exists();
    }
}
