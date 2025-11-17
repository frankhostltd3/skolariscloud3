<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Academic\ClassRoom;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\Academic\Enrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the teacher dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $teacher = Auth::user();

        // Get the teacher record (not just the user)
        $teacherRecord = \App\Models\Teacher::where('employee_record_id', $teacher->employee_record_id ?? null)
            ->orWhere('email', $teacher->email)
            ->first();

        if (!$teacherRecord) {
            // Fallback: try to find by user ID if teacher record doesn't exist
            $teacherRecord = \App\Models\Teacher::find($teacher->id);
        }

        // Classes where teacher is class teacher (stored with user ID)
        $classesAsClassTeacher = ClassRoom::where('class_teacher_id', $teacher->id)
            ->with([
                'academicYear',
                'students',
                'subjects' => function ($query) use ($teacher) {
                    $query->wherePivot('teacher_id', $teacher->id);
                },
            ])
            ->get();

        // Classes where teacher teaches at least one subject (based on class_subjects.teacher_id)
        $subjectClassIds = DB::table('class_subjects')
            ->where('teacher_id', $teacher->id)
            ->distinct()
            ->pluck('class_id')
            ->filter()
            ->values();

        $allocatedClasses = $subjectClassIds->isEmpty()
            ? collect()
            : ClassRoom::whereIn('id', $subjectClassIds)
                ->with([
                    'academicYear',
                    'students',
                    'subjects' => function ($query) use ($teacher) {
                        $query->wherePivot('teacher_id', $teacher->id);
                    },
                ])
                ->get();

        // Merge all classes (remove duplicates)
        $allClasses = $classesAsClassTeacher->merge($allocatedClasses)->unique('id');

        // Subjects allocated to teacher (derived from class_subjects)
        $allSubjects = Subject::whereHas('classes', function ($query) use ($teacher) {
                $query->where('class_subjects.teacher_id', $teacher->id);
            })
            ->with('educationLevel')
            ->get();

        // Today's timetable entries for this teacher
        $teacherIdForSchedule = $teacherRecord?->id ?? $teacher->id; // Prefer Teacher model ID if available
        $todayDow = now()->isoWeekday(); // 1=Mon..7=Sun
        $todaySchedule = \App\Models\TimetableEntry::where('teacher_id', $teacherIdForSchedule)
            ->where('day_of_week', $todayDow)
            ->with(['subject', 'class', 'stream'])
            ->orderBy('starts_at')
            ->get();

        // Calculate statistics
        $stats = [
            'classes' => $allClasses->count(),
            'subjects' => $allSubjects->count(),
            'students' => $allClasses->sum(function ($class) {
                return $class->students->count();
            }),
            'assignments' => 0, // Placeholder, implement if assignments model exists
            'attendance' => 0, // Placeholder, implement if attendance model exists
        ];

        // Recent grade entries by this teacher
        $recentGrades = Grade::where('teacher_id', $teacher->id)
            ->with(['student', 'subject', 'class'])
            ->latest()
            ->limit(5)
            ->get();

        // Classes needing attention (low enrollment or no subjects)
        $classesNeedingAttention = $allClasses->filter(function ($class) {
            return $class->students->count() < 5 || $class->subjects->count() === 0;
        });

        // Upcoming assessments (grades that need to be entered)
        $subjectIds = $allSubjects->pluck('id');
        $classIds = $allClasses->pluck('id');

        $studentsNeedingGrades = collect();
        if ($classIds->isNotEmpty() && $subjectIds->isNotEmpty()) {
            $studentsNeedingGrades = \App\Models\Academic\Enrollment::whereIn('class_id', $classIds)
                ->with(['student', 'class'])
                ->where('status', 'active')
                ->get();
        }

        return view('tenant.teacher.dashboard', compact(
            'stats',
            'allClasses',
            'allSubjects',
            'todaySchedule',
            'recentGrades',
            'classesNeedingAttention',
            'studentsNeedingGrades',
        ));
    }
}