<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Academic\Quiz;
use App\Models\Academic\QuizAttempt;
use App\Models\Academic\Enrollment;

class QuizzesController extends Controller
{
    // List available quizzes for the student's enrolled classes
    public function index(Request $request)
    {
        $student = Auth::user();
        $classIds = Enrollment::where('student_id', $student->id)->pluck('class_id')->all();
        if (empty($classIds)) {
            $quizzes = collect();
            $attempts = collect();
            return view('tenant.student.quizzes.index', compact('quizzes', 'attempts'));
        }

        $quizzes = Quiz::with(['classes'])
            ->where('is_published', true)
            ->whereHas('classes', function ($q) use ($classIds) {
                $q->whereIn('classes.id', $classIds);
            })
            ->latest()
            ->paginate(12)
            ->appends($request->query());

        // Map attempts for quick status display
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

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('student_id', $student->id)->first();
        return view('tenant.student.quizzes.show', compact('quiz', 'attempt'));
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

        $attempt = QuizAttempt::firstOrCreate(
            ['quiz_id' => $quiz->id, 'student_id' => $student->id],
            ['started_at' => now()]
        );
        if (!$attempt->started_at) {
            $attempt->started_at = now();
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

        $quiz->load(['questions' => function ($q) {
            $q->select('questions.*');
        }]);

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
        $attempt->save();
        // Auto-grade objective questions
        $attempt->autoGrade();

        return redirect()->route('tenant.student.quizzes.show', $quiz)->with('success', 'Quiz submitted.');
    }

    private function abortIfNotEligible(Quiz $quiz, int $studentId): void
    {
        // Must be published and assigned to one of student's classes
        abort_unless($quiz->is_published, 403);
        $classIds = Enrollment::where('student_id', $studentId)->pluck('class_id')->all();
        abort_if(empty($classIds), 403);
        $assigned = $quiz->classes()->whereIn('classes.id', $classIds)->exists();
        abort_unless($assigned, 403);
    }
}