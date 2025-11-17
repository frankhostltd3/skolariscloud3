<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Academic\ClassRoom;
use App\Models\Grade;
use App\Models\User;

class StudentController extends Controller
{
    /**
     * Show a list of the teacher's classes with quick links to students.
     */
    public function index()
    {
        $teacher = Auth::user();

        $classes = ClassRoom::where('class_teacher_id', $teacher->id)
            ->orWhereHas('subjects', function ($q) use ($teacher) {
                // Use the correct pivot table name in whereHas callback
                $q->where('class_subjects.teacher_id', $teacher->id);
            })
            ->withCount(['students', 'subjects'])
            ->get();

        return view('tenant.teacher.students.index', compact('classes'));
    }

    /**
     * Show a student profile tailored for teachers.
     */
    public function show(User $student)
    {
        $teacher = Auth::user();

        // Authorization: teacher must be class teacher or subject teacher for the student's class.
        // Prefer class_id from route/query when provided to avoid coupling to "current" enrollment only.
        $classId = request()->query('class_id');
        if ($classId) {
            $class = \App\Models\Academic\ClassRoom::find($classId);
            abort_if(!$class, 404, 'Class not found');
            // Ensure the student actually belongs to this class
            $belongsToClass = $student->enrollments()->where('class_id', $class->id)->exists();
            abort_if(!$belongsToClass, 403, 'Student is not enrolled in this class.');
        } else {
            $enrollment = $student->currentEnrollment()->with('class')->first();
            abort_if(!$enrollment, 404, 'Student has no current enrollment');
            $class = $enrollment->class;
        }

        $isClassTeacher = $class && $class->class_teacher_id === $teacher->id;
        $isSubjectTeacher = $class && $class->subjects()->wherePivot('teacher_id', $teacher->id)->exists();

        if (!$isClassTeacher && !$isSubjectTeacher) {
            abort(403, 'You are not authorized to view this student.');
        }

        // Load recent grades by this teacher and attendance summary
        $student->load(['enrollments.class', 'studentSubjects']);

    $recentGrades = Grade::where('student_id', $student->id)
            ->where('class_id', $class->id)
            ->where('teacher_id', $teacher->id)
            ->with(['subject'])
            ->latest('assessment_date')
            ->limit(10)
            ->get();

        $attendanceSummary = [
            'present' => \App\Models\Attendance::where('student_id', $student->id)->where('class_id', $class->id)->where('status', 'present')->count(),
            'absent' => \App\Models\Attendance::where('student_id', $student->id)->where('class_id', $class->id)->where('status', 'absent')->count(),
            'late' => \App\Models\Attendance::where('student_id', $student->id)->where('class_id', $class->id)->where('status', 'late')->count(),
            'excused' => \App\Models\Attendance::where('student_id', $student->id)->where('class_id', $class->id)->where('status', 'excused')->count(),
        ];

        return view('tenant.teacher.students.show', compact('student', 'class', 'recentGrades', 'attendanceSummary'));
    }
}