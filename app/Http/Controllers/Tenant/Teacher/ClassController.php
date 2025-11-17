<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Academic\ClassRoom;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\Academic\Enrollment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClassController extends Controller
{
    /**
     * Display teacher's assigned classes.
     */
    public function index()
    {
        $teacher = Auth::user();

        // Class ownership based on class_teacher_id
        // Classes where teacher is class teacher OR assigned to any subject in the class
        $classes = ClassRoom::where('class_teacher_id', $teacher->id)
            ->orWhereHas('subjects', function ($q) use ($teacher) {
                // Use the correct pivot table name in whereHas callback
                $q->where('class_subjects.teacher_id', $teacher->id);
            })
            ->with(['academicYear', 'students', 'subjects'])
            ->withCount(['students', 'subjects'])
            ->get();

        return view('tenant.teacher.classes.index', compact('classes'));
    }

    /**
     * Show details of a specific class.
     */
    public function show(ClassRoom $class)
    {
        $teacher = Auth::user();

        // Ensure teacher can only view their own classes
        $isClassTeacher = $class->class_teacher_id === $teacher->id;
        $isSubjectTeacher = $class->subjects()->wherePivot('teacher_id', $teacher->id)->exists();
        if (!$isClassTeacher && !$isSubjectTeacher) {
            abort(403, 'You are not authorized to view this class.');
        }

        $class->load([
            'academicYear',
            // 'students' already returns User models; no nested 'user' relation exists on User
            'students',
            'subjects' => function ($query) use ($teacher) {
                $query->wherePivot('teacher_id', $teacher->id);
            }
        ]);

        // Get recent grades for this class
        $recentGrades = Grade::where('class_id', $class->id)
            ->where('teacher_id', $teacher->id)
            ->with(['student', 'subject'])
            ->latest()
            ->limit(10)
            ->get();

        // Get attendance statistics (placeholder)
        $attendanceStats = [
            'total_students' => $class->students->count(),
            'present_today' => 0,
            'absent_today' => 0,
            'attendance_rate' => 0
        ];

        // Get students with low grades
        $studentsWithLowGrades = $this->getStudentsWithLowGrades($class, $teacher->id);

        return view('tenant.teacher.classes.show', compact(
            'class',
            'recentGrades',
            'attendanceStats',
            'studentsWithLowGrades'
        ));
    }

    /**
     * Show students in a class.
     */
    public function students(ClassRoom $class)
    {
        $teacher = Auth::user();

        $isClassTeacher = $class->class_teacher_id === $teacher->id;
        $isSubjectTeacher = $class->subjects()->wherePivot('teacher_id', $teacher->id)->exists();
        if (!$isClassTeacher && !$isSubjectTeacher) {
            abort(403, 'You are not authorized to view this class.');
        }

        $students = $class->students()
            ->with(['grades' => function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            }])
            ->get();

        // Calculate average percentage for each student (use model accessor percentage)
        $students->each(function ($student) {
            $grades = $student->grades;
            $student->averageGrade = $grades->count() > 0
                ? $grades->avg(function ($g) {
                    return $g->percentage;
                })
                : null;
            $student->gradeCount = $grades->count();
        });

        return view('tenant.teacher.classes.students', compact('class', 'students'));
    }

    /**
     * Show grades for a specific class.
     */
    public function grades(ClassRoom $class, Request $request)
    {
        $teacher = Auth::user();

        $isClassTeacher = $class->class_teacher_id === $teacher->id;
        $isSubjectTeacher = $class->subjects()->wherePivot('teacher_id', $teacher->id)->exists();
        if (!$isClassTeacher && !$isSubjectTeacher) {
            abort(403, 'You are not authorized to view this class.');
        }

        // Get teacher's subjects for this class
        $subjects = $class->subjects()
            ->wherePivot('teacher_id', $teacher->id)
            ->get();

        $query = Grade::where('class_id', $class->id)
            ->where('teacher_id', $teacher->id)
            ->with(['student', 'subject', 'semester']);

        // Filter by subject if selected
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter by assessment type if selected
        if ($request->filled('assessment_type')) {
            $query->where('assessment_type', $request->assessment_type);
        }

        // Date range filters
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        if ($startDate && $endDate) {
            $query->whereBetween('assessment_date', [\Carbon\Carbon::parse($startDate)->startOfDay(), \Carbon\Carbon::parse($endDate)->endOfDay()]);
        } elseif ($startDate) {
            $query->whereDate('assessment_date', '>=', \Carbon\Carbon::parse($startDate)->toDateString());
        } elseif ($endDate) {
            $query->whereDate('assessment_date', '<=', \Carbon\Carbon::parse($endDate)->toDateString());
        }

        // Clone query for summary before pagination
        $summaryQuery = (clone $query);
        $summaryCount = $summaryQuery->count();
        // Compute average percentage safely in SQL without relying on a DB column
        if ($summaryCount > 0) {
            $avgRow = (clone $query)
                ->selectRaw(Grade::percentageAvgExpression() . ' AS avg_pct')
                ->first();
            $summaryAvg = $avgRow && $avgRow->avg_pct !== null ? (float) $avgRow->avg_pct : 0.0;
        } else {
            $summaryAvg = 0.0;
        }

        $grades = $query->latest('assessment_date')->paginate(20)->appends($request->query());

        return view('tenant.teacher.classes.grades', compact(
            'class',
            'grades',
            'subjects',
            'summaryCount',
            'summaryAvg'
        ));
    }

    /**
     * Get students with low grades in the class.
     */
    private function getStudentsWithLowGrades($class, $teacherId)
    {
        $percentageExpr = Grade::percentageValueExpression();

        return $class->students()
            ->select('users.*')
            // Students who have any grade below threshold in this class by this teacher
            ->whereExists(function ($q) use ($teacherId, $class, $percentageExpr) {
                $q->from('grades')
                    ->whereColumn('users.id', 'grades.student_id')
                    ->where('grades.teacher_id', $teacherId)
                    ->where('grades.class_id', $class->id)
                    ->whereRaw($percentageExpr . ' < 70');
            })
            // Average grade percentage as a subselect
            ->selectSub(function ($q) use ($teacherId, $class, $percentageExpr) {
                $q->from('grades')
                    ->whereColumn('users.id', 'grades.student_id')
                    ->where('grades.teacher_id', $teacherId)
                    ->where('grades.class_id', $class->id)
                    ->selectRaw('AVG(' . $percentageExpr . ')');
            }, 'average_grade')
            ->having('average_grade', '<', 70)
            ->limit(5)
            ->get();
    }
}