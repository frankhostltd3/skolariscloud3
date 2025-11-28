<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\Academic\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class QuizzesController extends Controller
{
    // List available quizzes for the student's enrolled classes
    public function index(Request $request)
    {
        $student = Auth::user();
        $classIds = Enrollment::where('student_id', $student->id)
            ->pluck('class_id')
            ->filter()
            ->values();

        if ($classIds->isEmpty()) {
            $quizzes = collect();
            $attempts = collect();
            return view('tenant.student.quizzes.index', compact('quizzes', 'attempts'));
        }

        $classIdArray = $classIds->all();
        $hasPivotTable = Schema::connection('tenant')->hasTable('quiz_class');
        $hasClassColumn = Schema::connection('tenant')->hasColumn('quizzes', 'class_id');

        $quizQuery = Quiz::query()
            ->with(['class', 'subject', 'teacher'])
            ->withCount('questions')
            ->where(function ($query) {
                $query->where('is_published', true)
                    ->orWhere('status', 'published');
            })
            ->where(function ($query) use ($classIdArray, $hasPivotTable, $hasClassColumn) {
                if ($hasPivotTable) {
                    $query->whereHas('classes', function ($q) use ($classIdArray) {
                        $q->whereIn('classes.id', $classIdArray);
                    });

                    if ($hasClassColumn) {
                        $query->orWhereIn('class_id', $classIdArray);
                    }
                } elseif ($hasClassColumn) {
                    $query->whereIn('class_id', $classIdArray);
                } else {
                    // Fallback to no results when no mapping exists
                    $query->whereRaw('1 = 0');
                }
            });

        $hasAvailableFrom = Schema::connection('tenant')->hasColumn('quizzes', 'available_from');
        $hasStartAt = Schema::connection('tenant')->hasColumn('quizzes', 'start_at');

        if ($hasAvailableFrom) {
            $quizQuery->orderByDesc('available_from');
        } elseif ($hasStartAt) {
            $quizQuery->orderByDesc('start_at');
        } else {
            $quizQuery->orderByDesc('created_at');
        }

        $quizzes = $quizQuery
            ->paginate(12)
            ->appends($request->query());

        $attempts = QuizAttempt::where('student_id', $student->id)
            ->whereIn('quiz_id', $quizzes->pluck('id'))
            ->get()
            ->keyBy('quiz_id');

        return view('tenant.student.quizzes.index', compact('quizzes', 'attempts'));
    }

    // Show quiz details and allow starting/resuming
    public function show(Quiz $quiz)
    {
        $student = Auth::user();
        $this->abortIfNotEligible($quiz, $student->id);
        $quiz->load(['questions', 'teacher', 'class', 'subject']);

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->first();

        return view('tenant.student.quizzes.show', compact('quiz', 'attempt'));
    }

    public function downloadPdf(Quiz $quiz)
    {
        $student = Auth::user();
        $this->abortIfNotEligible($quiz, $student->id);
        $quiz->load(['questions', 'teacher', 'class', 'subject']);

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->first();

        $pdf = Pdf::loadView('tenant.student.quizzes.export-pdf', [
            'quiz' => $quiz,
            'attempt' => $attempt,
            'student' => $student,
        ])->setPaper('A4', 'portrait');

        $filename = Str::slug($quiz->title ?? 'quiz') . '-quiz.pdf';

        return $pdf->download($filename);
    }

    public function print(Quiz $quiz)
    {
        $student = Auth::user();
        $this->abortIfNotEligible($quiz, $student->id);
        $quiz->load(['questions', 'teacher', 'class', 'subject']);

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->first();

        return view('tenant.student.quizzes.print', [
            'quiz' => $quiz,
            'attempt' => $attempt,
            'student' => $student,
        ]);
    }

    // Start a quiz attempt (idempotent)
    public function start(Request $request, Quiz $quiz)
    {
        $student = Auth::user();
        $this->abortIfNotEligible($quiz, $student->id);
        // Block starting before the quiz window opens
        if ($quiz->start_at && now()->lt($quiz->start_at)) {
            return redirect()->route('tenant.student.quizzes.show', $quiz)->with('warning', 'This quiz is not yet open.');
        }

        if (!empty($quiz->max_attempts)) {
            $existingAttempts = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('student_id', $student->id)
                ->count();
            if ($existingAttempts >= $quiz->max_attempts) {
                return redirect()->route('tenant.student.quizzes.show', $quiz)
                    ->with('warning', 'You have reached the maximum number of attempts for this quiz.');
            }
        }

        $attempt = QuizAttempt::firstOrCreate(
            ['quiz_id' => $quiz->id, 'student_id' => $student->id],
            ['started_at' => now()]
        );
        if (!$attempt->started_at) {
            $attempt->started_at = now();
            if (Schema::connection('tenant')->hasColumn('quiz_attempts', 'status')) {
                $attempt->status = 'in_progress';
            }
            $attempt->save();
        }

        return redirect()->route('tenant.student.quizzes.take', $quiz);
    }

    // Take quiz page (with timer)
    public function take(Quiz $quiz)
    {
        $student = Auth::user();
        $this->abortIfNotEligible($quiz, $student->id);
        // Prevent taking before open
        if ($quiz->start_at && now()->lt($quiz->start_at)) {
            return redirect()->route('tenant.student.quizzes.show', $quiz)->with('warning', 'This quiz is not yet open.');
        }

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('student_id', $student->id)->first();
        if (!$attempt) {
            return redirect()->route('tenant.student.quizzes.show', $quiz)->with('warning', 'Please start the quiz first.');
        }
        if ($attempt->submitted_at) {
            return redirect()->route('tenant.student.quizzes.show', $quiz)->with('info', 'You have already submitted this quiz.');
        }

        $quiz->load('questions');

        // Compute remaining seconds for timer
        $remainingSeconds = null;
        if ($quiz->duration_minutes) {
            $deadlineByDuration = optional($attempt->started_at)->copy()?->addMinutes($quiz->duration_minutes);
            $deadline = $deadlineByDuration;
            if ($quiz->end_at) {
                if (!$deadline) {
                    $deadline = $quiz->end_at;
                } else {
                    // Use the earlier of duration deadline or quiz end_at
                    $deadline = $deadline->gt($quiz->end_at) ? $quiz->end_at : $deadline;
                }
            }
            if ($deadline) {
                $remainingSeconds = max(0, now()->diffInSeconds($deadline, false));
            }
        }

        $isLateWindow = $quiz->end_at && now()->greaterThan($quiz->end_at);

        return view('tenant.student.quizzes.take', compact('quiz', 'attempt', 'remainingSeconds', 'isLateWindow'));
    }

    // Submit the quiz answers
    public function submit(Request $request, Quiz $quiz)
    {
        $student = Auth::user();
        $this->abortIfNotEligible($quiz, $student->id);

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('student_id', $student->id)->firstOrFail();
        if ($attempt->submitted_at) {
            return redirect()->route('tenant.student.quizzes.show', $quiz)->with('info', 'Quiz already submitted.');
        }

        $answers = $request->input('answers', []);
        // Persist answers and submit
        $attempt->answers = $answers;
        $attempt->submitted_at = now();
        // Late flag for reporting if column exists
        if ($quiz->end_at && now()->gt($quiz->end_at)) {
            $attempt->is_late = true;
        }
        if (Schema::connection('tenant')->hasColumn('quiz_attempts', 'status')) {
            $attempt->status = 'submitted';
        }
        $attempt->save();
        // Auto-grade objective questions
        $attempt->autoGrade();

        return redirect()->route('tenant.student.quizzes.show', $quiz)->with('success', 'Quiz submitted.');
    }

    private function abortIfNotEligible(Quiz $quiz, int $studentId): void
    {
        $isPublished = (bool) ($quiz->is_published ?? false);
        if (!$isPublished && isset($quiz->status)) {
            $isPublished = $quiz->status === 'published';
        }
        abort_unless($isPublished, 403);

        $classIds = Enrollment::where('student_id', $studentId)
            ->pluck('class_id')
            ->filter()
            ->all();
        abort_if(empty($classIds), 403);

        $hasPivot = Schema::connection('tenant')->hasTable('quiz_class');
        $assigned = false;

        if ($hasPivot) {
            $assigned = $quiz->classes()->whereIn('classes.id', $classIds)->exists();
        }

        if (!$assigned && Schema::connection('tenant')->hasColumn('quizzes', 'class_id')) {
            $assigned = in_array($quiz->class_id, $classIds);
        }

        abort_unless($assigned, 403);
    }
}
