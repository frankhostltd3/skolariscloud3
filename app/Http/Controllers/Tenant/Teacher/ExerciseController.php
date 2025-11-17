<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Exercise;
use App\Models\ExerciseSubmission;
use App\Models\SchoolClass;
use App\Models\Subject;

class ExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
    // Eager load relationships and submission counts
    $exercises = Exercise::with(['class:id,name', 'subject:id,name'])
        ->withCount([
            'submissions',
            'submissions as pending_submissions_count' => function($query) {
                $query->whereNull('score');
            },
            'submissions as graded_submissions_count' => function($query) {
                $query->whereNotNull('score');
            }
        ])
        ->byTeacher(Auth::id())
        ->latest()
        ->paginate(15);
    
    // Optimized stats queries
    $teacherExercisesQuery = Exercise::byTeacher(Auth::id());
    $teacherSubmissionsQuery = ExerciseSubmission::whereHas('exercise', function($q) {
        $q->byTeacher(Auth::id());
    });
    
    $stats = [
        'total' => (clone $teacherExercisesQuery)->count(),
        'active' => (clone $teacherExercisesQuery)->active()->count(),
        'pending_grading' => (clone $teacherSubmissionsQuery)->whereNull('score')->count(),
        'total_submissions' => (clone $teacherSubmissionsQuery)->count(),
    ];
    
    return view('tenant.teacher.classroom.exercises.index', compact('exercises', 'stats'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    $classes = SchoolClass::orderBy('name')->get();
    $subjects = Subject::orderBy('name')->get();

        return view('tenant.teacher.classroom.exercises.create', compact('classes', 'subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request) {
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'instructions' => 'nullable|string',
        'class_id' => 'required|exists:school_classes,id',
        'subject_id' => 'required|exists:subjects,id',
        'due_date' => 'required|date|after:now',
        'max_score' => 'required|numeric|min:0',
        'allow_late_submission' => 'nullable|boolean',
        'late_penalty_percent' => 'nullable|numeric|min:0|max:100',
        'submission_type' => 'required|in:file,text,both',
    ]);
    
    $exercise = Exercise::create([
        'teacher_id' => Auth::id(),
        ...$validated,
        'allow_late_submission' => $request->has('allow_late_submission'),
    ]);
    
    return redirect()
        ->route('tenant.teacher.classroom.exercises.show', $exercise)
        ->with('success', 'Assignment created successfully!');
}


public function submissions(Request $request, Exercise $exercise) {
    $this->authorize('view', $exercise);
    
    // Eager load relationships with select optimization
    $query = $exercise->submissions()->with([
        'student:id,name,email,student_id',
        'gradedBy:id,name'
    ]);
    
    // Apply filters
    $filter = $request->get('filter', 'all');
    if ($filter === 'pending') {
        $query->whereNull('score');
    } elseif ($filter === 'graded') {
        $query->whereNotNull('score');
    } elseif ($filter === 'late') {
        $query->where('is_late', true);
    }
    
    $submissions = $query->latest()->paginate(20);
    
    // Optimized stats with single query using aggregate functions
    $submissionsStats = $exercise->submissions()
        ->selectRaw('
            COUNT(*) as total,
            COUNT(CASE WHEN score IS NOT NULL THEN 1 END) as graded,
            COUNT(CASE WHEN score IS NULL THEN 1 END) as pending,
            COUNT(CASE WHEN is_late = 1 THEN 1 END) as late
        ')
        ->first();
    
    $stats = [
        'total_students' => $exercise->class->students()->count(),
        'submitted' => $submissionsStats->total ?? 0,
        'graded' => $submissionsStats->graded ?? 0,
        'pending' => $submissionsStats->pending ?? 0,
        'late' => $submissionsStats->late ?? 0,
    ];
    
    return view('tenant.teacher.classroom.exercises.submissions', compact('exercise', 'submissions', 'stats', 'filter'));
}


public function grade(Request $request, Exercise $exercise, ExerciseSubmission $submission) {
    $this->authorize('update', $exercise);
    
    $validated = $request->validate([
        'score' => 'required|numeric|min:0|max:' . $exercise->max_score,
        'feedback' => 'nullable|string',
    ]);
    
    $submission->grade(
        $validated['score'],
        $validated['feedback'],
        Auth::id()
    );
    
    return back()->with('success', 'Submission graded successfully!');
}

    /**
     * Display the specified resource.
     */
    public function show(Exercise $exercise)
    {
        $this->authorize('view', $exercise);
        
        $exercise->load(['class', 'subject', 'teacher']);
        
        // Get submission statistics
        $stats = [
            'total_students' => $exercise->class->students()->count(),
            'submitted' => $exercise->submissions()->count(),
            'graded' => $exercise->submissions()->whereNotNull('score')->count(),
            'pending' => $exercise->submissions()->whereNull('score')->count(),
            'average_score' => $exercise->submissions()->whereNotNull('score')->avg('score'),
            'on_time' => $exercise->submissions()->where('submitted_at', '<=', $exercise->due_date)->count(),
            'late' => $exercise->submissions()->where('submitted_at', '>', $exercise->due_date)->count(),
        ];
        
        // Get recent submissions
        $recentSubmissions = $exercise->submissions()
            ->with('student')
            ->latest()
            ->take(5)
            ->get();
        
        return view('tenant.teacher.classroom.exercises.show', compact('exercise', 'stats', 'recentSubmissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exercise $exercise)
    {
        $this->authorize('update', $exercise);
        
    $classes = SchoolClass::orderBy('name')->get();
    $subjects = Subject::orderBy('name')->get();
        
        return view('tenant.teacher.classroom.exercises.edit', compact('exercise', 'classes', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exercise $exercise)
    {
        $this->authorize('update', $exercise);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'due_date' => 'required|date',
            'max_score' => 'required|numeric|min:0',
            'allow_late_submission' => 'nullable|boolean',
            'late_penalty_percent' => 'nullable|numeric|min:0|max:100',
            'submission_type' => 'required|in:file,text,both',
        ]);
        
        $exercise->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'instructions' => $validated['instructions'],
            'class_id' => $validated['class_id'],
            'subject_id' => $validated['subject_id'],
            'due_date' => $validated['due_date'],
            'max_score' => $validated['max_score'],
            'allow_late_submission' => $request->has('allow_late_submission'),
            'late_penalty_percent' => $validated['late_penalty_percent'] ?? 0,
            'submission_type' => $validated['submission_type'],
        ]);
        
        return redirect()
            ->route('tenant.teacher.classroom.exercises.show', $exercise)
            ->with('success', 'Assignment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exercise $exercise)
    {
        $this->authorize('delete', $exercise);
        
        // Check if there are any submissions
        $submissionsCount = $exercise->submissions()->count();
        
        if ($submissionsCount > 0) {
            return back()->with('error', "Cannot delete assignment with {$submissionsCount} submission(s). Please archive it instead.");
        }
        
        $exercise->delete();
        
        return redirect()
            ->route('tenant.teacher.classroom.exercises.index')
            ->with('success', 'Assignment deleted successfully!');
    }

    /**
     * Grade multiple submissions at once
     */
    public function bulkGrade(Request $request, Exercise $exercise)
    {
        $this->authorize('update', $exercise);
        
        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:' . $exercise->max_score,
            'feedback' => 'nullable|string',
            'submission_ids' => 'required|string',
        ]);
        
        $submissionIds = explode(',', $validated['submission_ids']);
        
        $submissions = ExerciseSubmission::whereIn('id', $submissionIds)
            ->where('exercise_id', $exercise->id)
            ->whereNull('score') // Only grade ungraded submissions
            ->get();
        
        $gradedCount = 0;
        foreach ($submissions as $submission) {
            $submission->grade(
                $validated['score'],
                $validated['feedback'],
                Auth::id()
            );
            $gradedCount++;
        }
        
        return back()->with('success', "Successfully graded {$gradedCount} submission(s)!");
    }

    /**
     * Download a student's submission file
     */
    public function downloadSubmission(ExerciseSubmission $submission)
    {
        $exercise = $submission->exercise;
        $this->authorize('view', $exercise);
        
        if (!$submission->file_path) {
            return back()->with('error', 'No file available for download.');
        }
        
        return \Storage::download($submission->file_path, $submission->file_name ?? 'submission');
    }
}
