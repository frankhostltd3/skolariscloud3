<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\ExerciseSubmission;
use App\Models\Student;
use App\Services\FileUploadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExerciseController extends Controller
{
    protected $fileUploadService;
    protected array $classCache = [];

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display all assignments for student
     */
    public function index(Request $request)
    {
        $student = Auth::user();
        $studentId = $student->id;

        $classIds = $this->resolveStudentClassIds($student);
        $filter = $request->get('filter', 'all');

        if ($classIds->isEmpty()) {
            $exercises = new LengthAwarePaginator([], 0, 15, $request->input('page', 1), [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);

            $stats = [
                'total' => 0,
                'pending' => 0,
                'submitted' => 0,
                'graded' => 0,
            ];

            return view('tenant.student.classroom.exercises.index', compact('exercises', 'stats', 'filter'));
        }

        // Eager load relationships with select optimization
        $query = Exercise::whereIn('class_id', $classIds)
            ->with([
                'class:id,name',
                'subject:id,name',
                'teacher:id,name'
            ])
            ->select('id', 'title', 'description', 'class_id', 'subject_id', 'teacher_id', 'due_date', 'max_score', 'allow_late_submission', 'late_penalty_percent');

        // Filter by status
        switch ($filter) {
            case 'pending':
                // Not submitted and not overdue
                $query->where('due_date', '>=', Carbon::now())
                    ->whereDoesntHave('submissions', function ($q) use ($studentId) {
                        $q->where('student_id', $studentId);
                    });
                break;

            case 'submitted':
                // Submitted
                $query->whereHas('submissions', function ($q) use ($studentId) {
                    $q->where('student_id', $studentId);
                });
                break;

            case 'graded':
                // Submitted and graded
                $query->whereHas('submissions', function ($q) use ($studentId) {
                    $q->where('student_id', $studentId)
                      ->whereNotNull('grade');
                });
                break;

            case 'overdue':
                // Not submitted and overdue
                $query->where('due_date', '<', Carbon::now())
                    ->whereDoesntHave('submissions', function ($q) use ($studentId) {
                        $q->where('student_id', $studentId);
                    });
                break;
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $exercises = $query->latest('due_date')->paginate(15);

        // Eager load submissions for the paginated results only
        $exerciseIds = $exercises->pluck('id');
        $submissions = ExerciseSubmission::where('student_id', $studentId)
            ->whereIn('exercise_id', $exerciseIds)
            ->select('id', 'exercise_id', 'student_id', 'score', 'grade', 'is_late', 'submitted_at', 'graded_at')
            ->get()
            ->keyBy('exercise_id');

        // Add submission status to each exercise
        $exercises->getCollection()->transform(function ($exercise) use ($submissions) {
            $exercise->student_submission = $submissions->get($exercise->id);
            return $exercise;
        });

        // Optimized stats with single aggregate query
        $baseQuery = Exercise::whereIn('class_id', $classIds);
        $submissionQuery = ExerciseSubmission::where('student_id', $studentId)
            ->whereHas('exercise', function ($q) use ($classIds) {
                $q->whereIn('class_id', $classIds);
            });

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)
                ->where('due_date', '>=', Carbon::now())
                ->whereDoesntHave('submissions', function ($q) use ($studentId) {
                    $q->where('student_id', $studentId);
                })
                ->count(),
            'submitted' => (clone $submissionQuery)->count(),
            'graded' => (clone $submissionQuery)->whereNotNull('grade')->count(),
        ];

        return view('tenant.student.classroom.exercises.index', compact('exercises', 'stats', 'filter'));
    }

    /**
     * Show a specific assignment
     */
    public function show($id)
    {
        $student = Auth::user();

        [$exercise, $submission] = $this->getExerciseForStudent($student, $id);

        $canSubmit = $exercise->canSubmit($student->id);
        $isLate = Carbon::now()->isAfter($exercise->due_date);

        return view('tenant.student.classroom.exercises.show', compact(
            'exercise',
            'submission',
            'canSubmit',
            'isLate'
        ));
    }

    public function downloadPdf($id)
    {
        $student = Auth::user();
        [$exercise, $submission] = $this->getExerciseForStudent($student, $id);

        $pdf = Pdf::loadView('tenant.student.classroom.exercises.export-pdf', [
            'exercise' => $exercise,
            'submission' => $submission,
            'student' => $student,
        ])->setPaper('A4', 'portrait');

        $filename = Str::slug($exercise->title ?? 'assignment') . '-assignment.pdf';

        return $pdf->download($filename);
    }

    public function printView($id)
    {
        $student = Auth::user();
        [$exercise, $submission] = $this->getExerciseForStudent($student, $id);

        return view('tenant.student.classroom.exercises.print', [
            'exercise' => $exercise,
            'submission' => $submission,
            'student' => $student,
        ]);
    }

    /**
     * Submit an assignment
     */
    public function submit(Request $request, $id)
    {
        $student = Auth::user();

        $classIds = $this->resolveStudentClassIds($student);

        if ($classIds->isEmpty()) {
            abort(403, 'You are not currently assigned to a class.');
        }

        $exercise = Exercise::whereIn('class_id', $classIds)
            ->findOrFail($id);

        // Check if already submitted
        $existingSubmission = $exercise->submissions()
            ->where('student_id', $student->id)
            ->first();

        if ($existingSubmission) {
            return back()->with('error', 'You have already submitted this assignment.');
        }

        // Validate request
        $validated = $request->validate([
            'submission_text' => 'nullable|string',
            'submission_file' => 'nullable|file|max:51200', // 50MB max
        ]);

        // At least one of text or file is required
        if (empty($validated['submission_text']) && !$request->hasFile('submission_file')) {
            return back()->with('error', 'Please provide either submission text or a file.');
        }

        DB::beginTransaction();
        try {
            $submissionData = [
                'exercise_id' => $exercise->id,
                'student_id' => $student->id,
                'submission_text' => $validated['submission_text'],
                'submitted_at' => Carbon::now(),
                'status' => 'submitted',
            ];

            // Handle file upload
            if ($request->hasFile('submission_file')) {
                $file = $request->file('submission_file');
                $path = $this->fileUploadService->upload(
                    $file,
                    'submissions',
                    'public'
                );

                $submissionData['file_path'] = $path;
                $submissionData['file_name'] = $file->getClientOriginalName();
                $submissionData['file_type'] = $file->getClientMimeType();
                $submissionData['file_size'] = $file->getSize();
            }

            // Check if late
            if (Carbon::now()->isAfter($exercise->due_date)) {
                $submissionData['is_late'] = true;
            }

            $submission = ExerciseSubmission::create($submissionData);

            DB::commit();

            return redirect()
                ->route('tenant.student.classroom.exercises.show', $exercise->id)
                ->with('success', 'Assignment submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Failed to submit assignment: ' . $e->getMessage());
        }
    }

    /**
     * Download student's own submission
     */
    public function downloadSubmission($id)
    {
        $student = Auth::user();

        $submission = ExerciseSubmission::where('student_id', $student->id)
            ->findOrFail($id);

        if (!$submission->file_path || !Storage::disk($submission->storage_disk ?? 'public')->exists($submission->file_path)) {
            return back()->with('error', 'Submission file not found.');
        }

        $filePath = Storage::disk($submission->storage_disk ?? 'public')->path($submission->file_path);
        $fileName = $submission->file_name ?? basename($submission->file_path);

        return response()->download($filePath, $fileName);
    }

    /**
     * Show student's grades
     */
    public function grades()
    {
        $student = Auth::user();

        $classIds = $this->resolveStudentClassIds($student);

        if ($classIds->isEmpty()) {
            $submissions = new LengthAwarePaginator([], 0, 15, request()->input('page', 1), [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);

            $stats = [
                'total_graded' => 0,
                'average_grade' => null,
                'highest_grade' => null,
                'lowest_grade' => null,
            ];

            return view('tenant.student.classroom.exercises.grades', compact('submissions', 'stats'));
        }

        $submissions = ExerciseSubmission::where('student_id', $student->id)
            ->whereHas('exercise', function ($q) use ($classIds) {
                $q->whereIn('class_id', $classIds);
            })
            ->whereNotNull('grade')
            ->with(['exercise.class', 'exercise.subject', 'exercise.teacher'])
            ->latest('graded_at')
            ->paginate(15);

        // Calculate statistics
        $stats = [
            'total_graded' => $submissions->total(),
            'average_grade' => ExerciseSubmission::where('student_id', $student->id)
                ->whereHas('exercise', function ($q) use ($classIds) {
                    $q->whereIn('class_id', $classIds);
                })
                ->whereNotNull('grade')
                ->avg('grade'),
            'highest_grade' => ExerciseSubmission::where('student_id', $student->id)
                ->whereHas('exercise', function ($q) use ($classIds) {
                    $q->whereIn('class_id', $classIds);
                })
                ->whereNotNull('grade')
                ->max('grade'),
            'lowest_grade' => ExerciseSubmission::where('student_id', $student->id)
                ->whereHas('exercise', function ($q) use ($classIds) {
                    $q->whereIn('class_id', $classIds);
                })
                ->whereNotNull('grade')
                ->min('grade'),
        ];

        // Round statistics
        $stats['average_grade'] = $stats['average_grade'] ? round($stats['average_grade'], 1) : null;

        return view('tenant.student.classroom.exercises.grades', compact('submissions', 'stats'));
    }

    protected function getExerciseForStudent($student, int $exerciseId): array
    {
        $classIds = $this->resolveStudentClassIds($student);

        if ($classIds->isEmpty()) {
            abort(403, 'You are not currently assigned to a class.');
        }

        $exercise = Exercise::whereIn('class_id', $classIds)
            ->with(['class', 'subject', 'teacher'])
            ->findOrFail($exerciseId);

        $submission = $exercise->submissions()
            ->where('student_id', $student->id)
            ->first();

        return [$exercise, $submission];
    }

    protected function resolveStudentClassIds($student): Collection
    {
        if (array_key_exists($student->id, $this->classCache)) {
            return $this->classCache[$student->id];
        }

        $classIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('class_id')
            ->filter()
            ->unique()
            ->values();

        if ($classIds->isEmpty()) {
            $profile = Student::select('class_id')
                ->where('email', $student->email)
                ->first();

            if ($profile?->class_id) {
                $classIds = collect([$profile->class_id]);
            }
        }

        return $this->classCache[$student->id] = $classIds ?? collect();
    }
}
