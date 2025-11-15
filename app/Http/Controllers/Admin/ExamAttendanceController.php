<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\User;
use App\Models\Academic\ClassRoom;
use App\Models\Academic\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamAttendanceController extends Controller
{
    /**
     * Display a listing of exam attendance sessions.
     */
    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $query = Attendance::forSchool($school->id)
            ->with(['class', 'subject', 'teacher'])
            ->where('attendance_type', 'exam')
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

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $attendances = $query->paginate(perPage());

        $classes = ClassRoom::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $subjects = Subject::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.exam-attendance.index', compact('attendances', 'classes', 'subjects', 'school'));
    }

    /**
     * Show the form for creating a new exam attendance session.
     */
    public function create(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $classes = ClassRoom::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $subjects = Subject::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.exam-attendance.create', compact('classes', 'subjects', 'school'));
    }

    /**
     * Store a newly created exam attendance session.
     */
    public function store(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'attendance_date' => 'required|date',
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['school_id'] = $school->id;
        $validated['teacher_id'] = auth()->id();
        $validated['attendance_type'] = 'exam';

        $attendance = Attendance::create($validated);

        return redirect()->route('admin.exam-attendance.mark', $attendance->id)
            ->with('success', 'Exam attendance session created. Please mark student attendance.');
    }

    /**
     * Show the form for marking exam attendance.
     */
    public function mark(Request $request, $id)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $attendance = Attendance::forSchool($school->id)
            ->with(['class', 'subject', 'records.student'])
            ->findOrFail($id);

        // Get students for this class
        $students = User::where('school_id', $school->id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'student');
            })
            ->where('class_id', $attendance->class_id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Get existing records
        $existingRecords = $attendance->records->keyBy('student_id');

        return view('admin.exam-attendance.mark', compact('attendance', 'students', 'existingRecords', 'school'));
    }

    /**
     * Save exam attendance records.
     */
    public function saveRecords(Request $request, $id)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $attendance = Attendance::forSchool($school->id)->findOrFail($id);

        $validated = $request->validate([
            'records' => 'required|array',
            'records.*.student_id' => 'required|exists:users,id',
            'records.*.status' => 'required|in:present,absent,late,excused',
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
            return redirect()->route('admin.exam-attendance.index')
                ->with('success', 'Exam attendance records saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save exam attendance: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified exam attendance session.
     */
    public function show(Request $request, $id)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $attendance = Attendance::forSchool($school->id)
            ->with(['class', 'subject', 'teacher', 'records.student'])
            ->findOrFail($id);

        $statistics = $attendance->getStatistics();

        return view('admin.exam-attendance.show', compact('attendance', 'statistics', 'school'));
    }

    /**
     * Remove the specified exam attendance session.
     */
    public function destroy(Request $request, $id)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $attendance = Attendance::forSchool($school->id)->findOrFail($id);
        $attendance->delete();

        return redirect()->route('admin.exam-attendance.index')
            ->with('success', 'Exam attendance session deleted successfully.');
    }
}
