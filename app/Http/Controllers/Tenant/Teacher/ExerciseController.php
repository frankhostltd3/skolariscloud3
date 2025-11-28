<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Models\Exercise;
use App\Models\ExerciseSubmission;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\AssignmentCreatedNotification;
use App\Notifications\AssignmentGradedNotification;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
        'content' => 'required|string',
        'class_id' => 'required|exists:classes,id',
        'subject_id' => 'required|exists:subjects,id',
        'due_date' => 'required|date|after:now',
        'max_score' => 'required|numeric|min:0',
        'allow_late_submission' => 'nullable|boolean',
        'late_penalty_percent' => 'nullable|numeric|min:0|max:100',
        'submission_type' => 'required|in:file,text,both',
        'attachments' => 'nullable|array',
        'attachments.*' => 'file|max:10240',
        'rubric' => 'nullable|array',
        'rubric.*.criterion' => 'required|string',
        'rubric.*.points' => 'required|numeric|min:0',
        'auto_grade' => 'nullable|boolean',
        'plagiarism_check' => 'nullable|boolean',
        'peer_review_enabled' => 'nullable|boolean',
        'peer_review_count' => 'nullable|integer|min:1|max:5',
    ]);

    DB::beginTransaction();
    try {
        // Handle file attachments
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('exercises/attachments', 'public');
                $attachmentPaths[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
        }

        $exercise = Exercise::create([
            'teacher_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'instructions' => $validated['instructions'],
            'content' => $validated['content'],
            'class_id' => $validated['class_id'],
            'subject_id' => $validated['subject_id'],
            'due_date' => $validated['due_date'],
            'max_score' => $validated['max_score'],
            'allow_late_submission' => $request->boolean('allow_late_submission'),
            'late_penalty_percent' => $validated['late_penalty_percent'] ?? 0,
            'submission_type' => $validated['submission_type'],
            'attachments' => $attachmentPaths,
            'rubric' => $validated['rubric'] ?? null,
            'auto_grade' => $request->boolean('auto_grade'),
            'plagiarism_check_enabled' => $request->boolean('plagiarism_check'),
            'peer_review_enabled' => $request->boolean('peer_review_enabled'),
            'peer_review_count' => $validated['peer_review_count'] ?? 1,
        ]);

        // Notify students
        $students = $exercise->class->students;
        if ($students->isNotEmpty()) {
            Notification::send($students, new AssignmentCreatedNotification($exercise));
        }

        DB::commit();

        return redirect()
            ->route('tenant.teacher.classroom.exercises.show', $exercise)
            ->with('success', 'Assignment created successfully and students notified!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->withErrors(['error' => 'Failed to create assignment: ' . $e->getMessage()]);
    }
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
            'class_id' => 'required|exists:classes,id',
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

        return Storage::download($submission->file_path, $submission->file_name ?? 'submission');
    }

    /**
     * Get analytics for the assignment
     */
    public function analytics(Exercise $exercise)
    {
        $this->authorize('view', $exercise);

        $submissions = $exercise->submissions()->with('student')->get();

        $analytics = [
            'overview' => [
                'total_students' => $exercise->class->students()->count(),
                'submitted' => $submissions->count(),
                'graded' => $submissions->whereNotNull('score')->count(),
                'pending' => $submissions->whereNull('score')->count(),
                'on_time' => $submissions->where('submitted_at', '<=', $exercise->due_date)->count(),
                'late' => $submissions->where('submitted_at', '>', $exercise->due_date)->count(),
                'not_submitted' => $exercise->class->students()->count() - $submissions->count(),
            ],
            'scores' => [
                'average' => round($submissions->whereNotNull('score')->avg('score'), 2),
                'median' => $this->calculateMedian($submissions->whereNotNull('score')->pluck('score')),
                'highest' => $submissions->whereNotNull('score')->max('score'),
                'lowest' => $submissions->whereNotNull('score')->min('score'),
                'std_deviation' => $this->calculateStdDev($submissions->whereNotNull('score')->pluck('score')),
            ],
            'distribution' => [
                'excellent' => $submissions->where('score', '>=', $exercise->max_score * 0.9)->count(),
                'good' => $submissions->whereBetween('score', [$exercise->max_score * 0.7, $exercise->max_score * 0.89])->count(),
                'satisfactory' => $submissions->whereBetween('score', [$exercise->max_score * 0.5, $exercise->max_score * 0.69])->count(),
                'needs_improvement' => $submissions->where('score', '<', $exercise->max_score * 0.5)->count(),
            ],
            'time_analysis' => [
                'early_submissions' => $submissions->where('submitted_at', '<', Carbon::parse($exercise->due_date)->subDays(2))->count(),
                'last_day_submissions' => $submissions->whereBetween('submitted_at', [
                    Carbon::parse($exercise->due_date)->startOfDay(),
                    Carbon::parse($exercise->due_date)->endOfDay()
                ])->count(),
                'average_days_before_due' => $this->calculateAverageDaysBeforeDue($submissions, $exercise->due_date),
            ],
        ];

        return view('tenant.teacher.classroom.exercises.analytics', compact('exercise', 'analytics', 'submissions'));
    }

    /**
     * Export assignment data
     */
    public function export(Request $request, Exercise $exercise)
    {
        $this->authorize('view', $exercise);

        $format = $request->get('format', 'csv');
        $submissions = $exercise->submissions()->with(['student', 'gradedBy'])->get();

        if ($format === 'csv') {
            return $this->exportToCsv($exercise, $submissions);
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($exercise, $submissions);
        }

        return back()->with('error', 'Invalid export format.');
    }

    /**
     * Duplicate an assignment
     */
    public function duplicate(Exercise $exercise)
    {
        $this->authorize('create', Exercise::class);

        $newExercise = $exercise->replicate();
        $newExercise->title = $exercise->title . ' (Copy)';
        $newExercise->teacher_id = Auth::id();
        $newExercise->due_date = null;
        $newExercise->created_at = now();
        $newExercise->updated_at = now();
        $newExercise->save();

        // Copy questions if any
        foreach ($exercise->questions as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->exercise_id = $newExercise->id;
            $newQuestion->save();
        }

        return redirect()
            ->route('tenant.teacher.classroom.exercises.edit', $newExercise)
            ->with('success', 'Assignment duplicated successfully! Please update the due date.');
    }

    /**
     * Archive an assignment
     */
    public function archive(Exercise $exercise)
    {
        $this->authorize('update', $exercise);

        $exercise->update(['status' => 'archived']);

        return back()->with('success', 'Assignment archived successfully.');
    }

    /**
     * Reopen an assignment
     */
    public function reopen(Exercise $exercise)
    {
        $this->authorize('update', $exercise);

        $exercise->update(['status' => 'active', 'due_date' => Carbon::now()->addWeek()]);

        return back()->with('success', 'Assignment reopened successfully with a new due date.');
    }

    /**
     * Auto-grade submissions using AI or predefined criteria
     */
    public function autoGrade(Exercise $exercise)
    {
        $this->authorize('update', $exercise);

        if (!$exercise->auto_grade) {
            return back()->with('error', 'Auto-grading is not enabled for this assignment.');
        }

        $ungradedSubmissions = $exercise->submissions()->whereNull('score')->get();
        $gradedCount = 0;

        foreach ($ungradedSubmissions as $submission) {
            // Auto-grade based on rubric or objective questions
            if ($exercise->questions()->where('type', 'objective')->exists()) {
                $score = $this->calculateObjectiveScore($submission, $exercise);
                $submission->grade($score, 'Auto-graded based on answer key', Auth::id());
                $gradedCount++;
            }
        }

        return back()->with('success', "Auto-graded {$gradedCount} submission(s) successfully!");
    }

    /**
     * Run plagiarism detection
     */
    public function checkPlagiarism(Exercise $exercise)
    {
        $this->authorize('view', $exercise);

        if (!$exercise->plagiarism_check_enabled) {
            return back()->with('error', 'Plagiarism check is not enabled for this assignment.');
        }

        $submissions = $exercise->submissions;
        $results = [];

        // Compare submissions against each other
        foreach ($submissions as $submission1) {
            foreach ($submissions as $submission2) {
                if ($submission1->id >= $submission2->id) continue;

                $similarity = $this->calculateSimilarity(
                    $submission1->submission_text ?? '',
                    $submission2->submission_text ?? ''
                );

                if ($similarity > 70) {
                    $results[] = [
                        'student1' => $submission1->student->name,
                        'student2' => $submission2->student->name,
                        'similarity' => $similarity,
                    ];
                }
            }
        }

        return view('tenant.teacher.classroom.exercises.plagiarism', compact('exercise', 'results'));
    }

    /**
     * Send reminder to students who haven't submitted
     */
    public function sendReminder(Exercise $exercise)
    {
        $this->authorize('update', $exercise);

        $submittedStudentIds = $exercise->submissions()->pluck('student_id');
        $pendingStudents = $exercise->class->students()
            ->whereNotIn('id', $submittedStudentIds)
            ->get();

        if ($pendingStudents->isEmpty()) {
            return back()->with('info', 'All students have already submitted.');
        }

        foreach ($pendingStudents as $student) {
            // Send reminder notification
            $student->notify(new \App\Notifications\AssignmentReminderNotification($exercise));
        }

        return back()->with('success', "Reminder sent to {$pendingStudents->count()} student(s).");
    }

    /**
     * Helper: Calculate median
     */
    private function calculateMedian($values)
    {
        $count = $values->count();
        if ($count === 0) return 0;

        $sorted = $values->sort()->values();
        $middle = floor($count / 2);

        if ($count % 2 == 0) {
            return ($sorted[$middle - 1] + $sorted[$middle]) / 2;
        }

        return $sorted[$middle];
    }

    /**
     * Helper: Calculate standard deviation
     */
    private function calculateStdDev($values)
    {
        $count = $values->count();
        if ($count === 0) return 0;

        $mean = $values->avg();
        $variance = $values->reduce(function ($carry, $value) use ($mean) {
            return $carry + pow($value - $mean, 2);
        }, 0) / $count;

        return round(sqrt($variance), 2);
    }

    /**
     * Helper: Calculate average days before due date
     */
    private function calculateAverageDaysBeforeDue($submissions, $dueDate)
    {
        if ($submissions->isEmpty()) return 0;

        $totalDays = $submissions->reduce(function ($carry, $submission) use ($dueDate) {
            $days = Carbon::parse($dueDate)->diffInDays($submission->submitted_at, false);
            return $carry + $days;
        }, 0);

        return round($totalDays / $submissions->count(), 1);
    }

    /**
     * Helper: Export to CSV
     */
    private function exportToCsv($exercise, $submissions)
    {
        $filename = Str::slug($exercise->title) . '-submissions-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($exercise, $submissions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student Name', 'Student ID', 'Submitted At', 'Score', 'Max Score', 'Percentage', 'Status', 'Feedback']);

            foreach ($submissions as $submission) {
                fputcsv($file, [
                    $submission->student->name,
                    $submission->student->student_id ?? 'N/A',
                    $submission->submitted_at->format('Y-m-d H:i:s'),
                    $submission->score ?? 'Not Graded',
                    $exercise->max_score,
                    $submission->score_percentage ?? 'N/A',
                    $submission->status_label,
                    $submission->feedback ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper: Export to PDF
     */
    private function exportToPdf($exercise, $submissions)
    {
        $pdf = \PDF::loadView('tenant.teacher.classroom.exercises.export-pdf', compact('exercise', 'submissions'));
        return $pdf->download(Str::slug($exercise->title) . '-submissions.pdf');
    }

    /**
     * Helper: Calculate objective score
     */
    private function calculateObjectiveScore($submission, $exercise)
    {
        $totalScore = 0;
        $answers = json_decode($submission->answers ?? '[]', true);

        foreach ($exercise->questions()->where('type', 'objective')->get() as $question) {
            $studentAnswer = $answers[$question->id] ?? null;
            if ($studentAnswer == $question->correct_answer) {
                $totalScore += $question->marks;
            }
        }

        return $totalScore;
    }

    /**
     * Helper: Calculate text similarity (simple implementation)
     */
    private function calculateSimilarity($text1, $text2)
    {
        if (empty($text1) || empty($text2)) return 0;

        similar_text(strtolower($text1), strtolower($text2), $percent);
        return round($percent, 2);
    }
}
