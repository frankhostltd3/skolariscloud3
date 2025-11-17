<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\VirtualClass;
use App\Models\LearningMaterial;
use App\Models\Exercise;
use App\Models\ExerciseSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClassroomController extends Controller
{
    /**
     * Display the student's classroom dashboard
     */
    public function index()
    {
        $student = Auth::user();
        $studentId = $student->id;

        // Get student's enrolled classes with eager loading
        $enrolledClasses = $student->enrollments()
            ->with(['schoolClass:id,name,grade_level,section', 'academicYear:id,name,is_current'])
            ->where('status', 'active')
            ->get();

        $classIds = $enrolledClasses->pluck('school_class_id');

        // Upcoming virtual classes with optimized eager loading
        $upcomingClasses = VirtualClass::whereIn('class_id', $classIds)
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->where('scheduled_at', '>=', Carbon::now()->subHours(3))
            ->orderBy('scheduled_at', 'asc')
            ->with([
                'class:id,name,grade_level,section',
                'subject:id,name,code',
                'teacher:id,name,email'
            ])
            ->take(5)
            ->get();

        // Recent learning materials with eager loading
        $recentMaterials = LearningMaterial::whereIn('class_id', $classIds)
            ->latest()
            ->with([
                'class:id,name',
                'subject:id,name',
                'teacher:id,name'
            ])
            ->select('id', 'title', 'type', 'class_id', 'subject_id', 'teacher_id', 'created_at')
            ->take(10)
            ->get();

        // Pending assignments with eager loading
        $pendingAssignments = Exercise::whereIn('class_id', $classIds)
            ->where('due_date', '>=', Carbon::now())
            ->whereDoesntHave('submissions', function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->orderBy('due_date', 'asc')
            ->with([
                'class:id,name',
                'subject:id,name',
                'teacher:id,name'
            ])
            ->select('id', 'title', 'class_id', 'subject_id', 'teacher_id', 'due_date', 'max_score')
            ->take(5)
            ->get();

        // Submitted assignments with eager loading and select optimization
        $submittedAssignments = ExerciseSubmission::where('student_id', $studentId)
            ->whereHas('exercise', function ($query) use ($classIds) {
                $query->whereIn('class_id', $classIds);
            })
            ->latest()
            ->with([
                'exercise' => function($query) {
                    $query->with([
                        'class:id,name',
                        'subject:id,name'
                    ])->select('id', 'title', 'class_id', 'subject_id', 'max_score');
                }
            ])
            ->select('id', 'exercise_id', 'student_id', 'score', 'grade', 'submitted_at')
            ->take(5)
            ->get();

        // Calculate statistics with optimized queries
        // Cache class IDs query base
        $classesQuery = VirtualClass::whereIn('class_id', $classIds);
        $materialsQuery = LearningMaterial::whereIn('class_id', $classIds);
        
        $stats = [
            'total_classes' => (clone $classesQuery)->count(),
            'attended_classes' => (clone $classesQuery)
                ->whereHas('attendances', function ($query) use ($studentId) {
                    $query->where('student_id', $studentId)
                          ->whereIn('status', ['present', 'late']);
                })
                ->count(),
            'total_materials' => (clone $materialsQuery)->count(),
            'accessed_materials' => (clone $materialsQuery)
                ->whereHas('accesses', function ($query) use ($studentId) {
                    $query->where('student_id', $studentId);
                })
                ->count(),
            'total_assignments' => Exercise::whereIn('class_id', $classIds)->count(),
            'pending_assignments' => $pendingAssignments->count(),
            'submitted_assignments' => ExerciseSubmission::where('student_id', $studentId)
                ->whereHas('exercise', function ($query) use ($classIds) {
                    $query->whereIn('class_id', $classIds);
                })
                ->count(),
            'graded_assignments' => ExerciseSubmission::where('student_id', $studentId)
                ->whereHas('exercise', function ($query) use ($classIds) {
                    $query->whereIn('class_id', $classIds);
                })
                ->whereNotNull('grade')
                ->count(),
        ];

        // Calculate average grade
        $averageGrade = ExerciseSubmission::where('student_id', $studentId)
            ->whereHas('exercise', function ($query) use ($classIds) {
                $query->whereIn('class_id', $classIds);
            })
            ->whereNotNull('grade')
            ->avg('grade');

        $stats['average_grade'] = $averageGrade ? round($averageGrade, 1) : null;

        return view('tenant.student.classroom.index', compact(
            'upcomingClasses',
            'recentMaterials',
            'pendingAssignments',
            'submittedAssignments',
            'stats',
            'enrolledClasses'
        ));
    }

    /**
     * Show all enrolled classes
     */
    public function classes()
    {
        $student = Auth::user();
        
        $enrolledClasses = $student->enrollments()
            ->with(['schoolClass.grade', 'academicYear'])
            ->where('status', 'active')
            ->get();

        return view('tenant.student.classroom.classes', compact('enrolledClasses'));
    }

    /**
     * Show class details
     */
    public function showClass($classId)
    {
        $student = Auth::user();

        // Verify student is enrolled in this class
        $enrollment = $student->enrollments()
            ->where('school_class_id', $classId)
            ->where('status', 'active')
            ->firstOrFail();

        $class = $enrollment->schoolClass;
        $class->load('grade', 'section');

        // Get class statistics
        $stats = [
            'total_classes' => VirtualClass::where('class_id', $classId)->count(),
            'attended' => VirtualClass::where('class_id', $classId)
                ->whereHas('attendances', function ($query) use ($student) {
                    $query->where('student_id', $student->id)
                          ->whereIn('status', ['present', 'late']);
                })
                ->count(),
            'materials' => LearningMaterial::where('class_id', $classId)->count(),
            'assignments' => Exercise::where('class_id', $classId)->count(),
        ];

        // Recent activity
        $recentClasses = VirtualClass::where('class_id', $classId)
            ->latest('scheduled_at')
            ->with(['subject', 'teacher'])
            ->take(5)
            ->get();

        $recentMaterials = LearningMaterial::where('class_id', $classId)
            ->latest()
            ->with(['subject', 'teacher'])
            ->take(5)
            ->get();

        return view('tenant.student.classroom.show-class', compact(
            'class',
            'stats',
            'recentClasses',
            'recentMaterials'
        ));
    }
}
