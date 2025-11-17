<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use App\Models\VirtualClass;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VirtualClassController extends Controller
{
    public function index(Request $request)
    {
        // Eager load relationships to prevent N+1 queries
        $query = VirtualClass::with(['class', 'subject', 'teacher:id,name'])
            ->withCount('attendances')
            ->byTeacher(Auth::id())
            ->latest();

        // Filter by status
        if ($request->has('status')) {
            switch ($request->status) {
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'live':
                    $query->live();
                    break;
                case 'completed':
                    $query->completed();
                    break;
            }
        }

        $virtualClasses = $query->paginate(15);

        // Optimized statistics with single queries
        $teacherClassesQuery = VirtualClass::byTeacher(Auth::id());
        
        $stats = [
            'total' => $teacherClassesQuery->count(),
            'scheduled' => (clone $teacherClassesQuery)->where('status', 'scheduled')->count(),
            'completed' => (clone $teacherClassesQuery)->completed()->count(),
            'total_participants' => (clone $teacherClassesQuery)
                ->withCount('attendances')
                ->get()
                ->sum('attendances_count'),
            'total_hours' => round((clone $teacherClassesQuery)->sum('duration_minutes') / 60, 1),
        ];

        return view('tenant.teacher.classroom.virtual.index', compact('virtualClasses', 'stats'));
    }

    public function create()
    {
        $teacher = Auth::user();
        
        // Get classes assigned to this teacher
        $classes = SchoolClass::orderBy('grade_level')
            ->orderBy('section')
            ->get();

        // Get subjects taught by this teacher
        $subjects = Subject::orderBy('name')->get();

        return view('tenant.teacher.classroom.virtual.create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'platform' => 'required|in:zoom,google_meet,microsoft_teams,youtube,other',
            'meeting_id' => 'nullable|string|max:255',
            'meeting_password' => 'nullable|string|max:255',
            'meeting_url' => 'nullable|url|max:500',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required',
            'duration_minutes' => 'required|integer|min:15|max:300',
            'auto_record' => 'nullable|boolean',
            'is_recurring' => 'nullable|boolean',
            'recurrence_pattern' => 'nullable|in:daily,weekly,monthly',
            'recurrence_end_date' => 'nullable|date|after:scheduled_date',
        ]);

        DB::beginTransaction();
        try {
            // Combine date and time
            $scheduledAt = \Carbon\Carbon::parse($request->scheduled_date . ' ' . $request->scheduled_time);

            $virtualClass = VirtualClass::create([
                'teacher_id' => Auth::id(),
                'class_id' => $validated['class_id'],
                'subject_id' => $validated['subject_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'platform' => $validated['platform'],
                'meeting_id' => $validated['meeting_id'],
                'meeting_password' => $validated['meeting_password'],
                'meeting_url' => $validated['meeting_url'],
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $validated['duration_minutes'],
                'status' => 'scheduled',
                'auto_record' => $request->has('auto_record'),
                'is_recurring' => $request->has('is_recurring'),
                'recurrence_pattern' => $validated['recurrence_pattern'] ?? null,
                'recurrence_end_date' => $validated['recurrence_end_date'] ?? null,
            ]);

            // TODO: Send notifications to students

            DB::commit();

            return redirect()
                ->route('tenant.teacher.classroom.virtual.show', $virtualClass)
                ->with('success', 'Virtual class scheduled successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to schedule virtual class: ' . $e->getMessage());
        }
    }

    public function show(VirtualClass $virtual)
    {
        // Ensure teacher owns this virtual class
        if ($virtual->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this virtual class');
        }

        // Load relationships
        $virtual->load(['class', 'subject', 'teacher:id,name']);

        // Get attendance records with pagination
        $attendanceRecords = $virtual->attendances()
            ->with('student:id,name,email')
            ->latest()
            ->paginate(20);

        // Calculate attendance statistics
        $totalAttendances = $virtual->attendances()->count();
        $presentCount = $virtual->attendances()->where('was_present', true)->count();
        
        $attendanceStats = [
            'total' => $totalAttendances,
            'present' => $presentCount,
            'absent' => $totalAttendances - $presentCount,
        ];

        // Calculate attendance rate
        $attendanceRate = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 1) : 0;

        return view('tenant.teacher.classroom.virtual.show', compact(
            'virtual',
            'attendanceRecords',
            'attendanceStats',
            'attendanceRate'
        ));
    }

    public function edit(VirtualClass $virtual)
    {
        $this->authorize('update', $virtual);

        $classes = SchoolClass::orderBy('grade_level')
            ->orderBy('section')
            ->get();

        $subjects = Subject::orderBy('name')->get();

        return view('tenant.teacher.classroom.virtual.edit', compact('virtual', 'classes', 'subjects'));
    }

    public function update(Request $request, VirtualClass $virtual)
    {
        $this->authorize('update', $virtual);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'platform' => 'required|in:zoom,google_meet,microsoft_teams,youtube,other',
            'meeting_id' => 'nullable|string|max:255',
            'meeting_password' => 'nullable|string|max:255',
            'meeting_url' => 'nullable|url|max:500',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required',
            'duration_minutes' => 'required|integer|min:15|max:300',
            'auto_record' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $scheduledAt = \Carbon\Carbon::parse($request->scheduled_date . ' ' . $request->scheduled_time);

            $virtual->update([
                'class_id' => $validated['class_id'],
                'subject_id' => $validated['subject_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'platform' => $validated['platform'],
                'meeting_id' => $validated['meeting_id'],
                'meeting_password' => $validated['meeting_password'],
                'meeting_url' => $validated['meeting_url'],
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $validated['duration_minutes'],
                'auto_record' => $request->has('auto_record'),
            ]);

            DB::commit();

            return redirect()
                ->route('tenant.teacher.classroom.virtual.show', $virtual)
                ->with('success', 'Virtual class updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update virtual class: ' . $e->getMessage());
        }
    }

    public function destroy(VirtualClass $virtual)
    {
        $this->authorize('delete', $virtual);

        try {
            $virtual->delete();

            return redirect()
                ->route('tenant.teacher.classroom.virtual.index')
                ->with('success', 'Virtual class deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete virtual class: ' . $e->getMessage());
        }
    }

    public function start(VirtualClass $virtual)
    {
        $this->authorize('update', $virtual);

        if ($virtual->status !== 'scheduled') {
            return back()->with('error', 'This class cannot be started.');
        }

        $virtual->start();

        return redirect()
            ->route('tenant.teacher.classroom.virtual.show', $virtual)
            ->with('success', 'Virtual class started! Students can now join.');
    }

    public function end(VirtualClass $virtual)
    {
        $this->authorize('update', $virtual);

        if ($virtual->status !== 'live') {
            return back()->with('error', 'This class is not live.');
        }

        $virtual->end();

        return redirect()
            ->route('tenant.teacher.classroom.virtual.show', $virtual)
            ->with('success', 'Virtual class ended successfully!');
    }

    public function cancel(VirtualClass $virtual)
    {
        $this->authorize('update', $virtual);

        if ($virtual->status === 'completed') {
            return back()->with('error', 'Cannot cancel a completed class.');
        }

        $virtual->cancel();

        // TODO: Send cancellation notifications to students

        return redirect()
            ->route('tenant.teacher.classroom.virtual.index')
            ->with('success', 'Virtual class cancelled successfully!');
    }

    public function attendance(VirtualClass $virtual)
    {
        $this->authorize('view', $virtual);

        $virtual->load(['class.activeEnrollments.student', 'attendances.student']);

        $students = $virtual->class->activeEnrollments->map(function ($enrollment) use ($virtual) {
            $attendance = $virtual->attendances->firstWhere('student_id', $enrollment->student_id);
            
            return [
                'student' => $enrollment->student,
                'attendance' => $attendance,
                'attended' => $attendance !== null,
            ];
        });

        return view('tenant.teacher.classroom.virtual.attendance', compact('virtual', 'students'));
    }
}
