<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\User;
use App\Models\Academic\ClassRoom;
use App\Models\Academic\ClassStream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance sessions.
     */
    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $query = Attendance::forSchool($school->id)
            ->with(['class', 'classStream', 'subject', 'teacher'])
            ->where('attendance_type', 'classroom')
            ->orderBy('attendance_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('attendance_date', $request->date);
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $attendances = $query->paginate(perPage());

        $classes = ClassRoom::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.attendance.index', compact('attendances', 'classes', 'school'));
    }

    /**
     * Show the form for creating a new attendance session.
     */
    public function create(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $classes = ClassRoom::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.attendance.create', compact('classes', 'school'));
    }

    /**
     * Store a newly created attendance session.
     */
    public function store(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'class_stream_id' => 'nullable|exists:class_streams,id',
            'attendance_date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['school_id'] = $school->id;
        $validated['teacher_id'] = auth()->id();
        $validated['attendance_type'] = 'classroom';

        $attendance = Attendance::create($validated);

        return redirect()->route('admin.attendance.mark', $attendance->id)
            ->with('success', 'Attendance session created. Please mark student attendance.');
    }

    /**
     * Show the form for marking attendance.
     */
    public function mark(Request $request, $id)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $attendance = Attendance::forSchool($school->id)
            ->with(['class', 'classStream', 'records.student'])
            ->findOrFail($id);

        // Get students for this class
        $studentsQuery = User::where('school_id', $school->id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'student');
            })
            ->where('class_id', $attendance->class_id);

        if ($attendance->class_stream_id) {
            $studentsQuery->where('class_stream_id', $attendance->class_stream_id);
        }

        $students = $studentsQuery->orderBy('last_name')->orderBy('first_name')->get();

        // Get existing records
        $existingRecords = $attendance->records->keyBy('student_id');

        return view('admin.attendance.mark', compact('attendance', 'students', 'existingRecords', 'school'));
    }

    /**
     * Save attendance records.
     */
    public function saveRecords(Request $request, $id)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $attendance = Attendance::forSchool($school->id)->findOrFail($id);

        $validated = $request->validate([
            'records' => 'required|array',
            'records.*.student_id' => 'required|exists:users,id',
            'records.*.status' => 'required|in:present,absent,late,excused,sick,half_day',
            'records.*.arrival_time' => 'nullable|date_format:H:i',
            'records.*.notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['records'] as $recordData) {
                AttendanceRecord::updateOrCreate(
                    [
                        'attendance_id' => $attendance->id,
                        'student_id' => $recordData['student_id'],
                    ],
                    [
                        'status' => $recordData['status'],
                        'arrival_time' => $recordData['arrival_time'] ?? null,
                        'notes' => $recordData['notes'] ?? null,
                    ]
                );
            }

            DB::commit();
            return redirect()->route('admin.attendance.index')
                ->with('success', 'Attendance records saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save attendance records: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified attendance session.
     */
    public function show(Request $request, $id)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $attendance = Attendance::forSchool($school->id)
            ->with(['class', 'classStream', 'subject', 'teacher', 'records.student'])
            ->findOrFail($id);

        $statistics = $attendance->getStatistics();

        return view('admin.attendance.show', compact('attendance', 'statistics', 'school'));
    }

    /**
     * Show the kiosk mode for fingerprint/self-service attendance.
     */
    public function kiosk(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        return view('admin.attendance.kiosk', compact('school'));
    }

    /**
     * Process kiosk check-in.
     */
    public function kioskCheckIn(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
        ]);

        // Find or create today's attendance session
        $attendance = Attendance::firstOrCreate([
            'school_id' => $school->id,
            'attendance_date' => $validated['attendance_date'],
            'attendance_type' => 'general',
        ]);

        $record = AttendanceRecord::updateOrCreate(
            [
                'attendance_id' => $attendance->id,
                'student_id' => $validated['student_id'],
            ],
            [
                'status' => 'present',
                'arrival_time' => now()->format('H:i'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance recorded successfully',
            'record' => $record,
        ]);
    }

    /**
     * Remove the specified attendance session.
     */
    public function destroy(Request $request, $id)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $attendance = Attendance::forSchool($school->id)->findOrFail($id);
        $attendance->delete();

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance session deleted successfully.');
    }
}
