<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\OnlineExam;
use App\Models\OnlineExamAttempt;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ExamsController extends Controller
{
    protected array $classCache = [];

    public function index()
    {
        $student = Auth::user();
        $classIds = $this->resolveStudentClassIds($student);

        if ($classIds->isEmpty()) {
            $exams = new LengthAwarePaginator([], 0, 10, request()->input('page', 1), [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);

            return view('tenant.student.exams.index', [
                'exams' => $exams,
                'attempts' => collect(),
            ]);
        }

        // Fetch exams assigned to the student's class
        $exams = OnlineExam::whereIn('class_id', $classIds)
            ->whereIn('status', ['scheduled', 'active', 'completed'])
            ->orderBy('starts_at', 'desc')
            ->paginate(10);

        // Fetch student's attempts for these exams
        $attempts = OnlineExamAttempt::where('student_id', $student->id)
            ->whereIn('online_exam_id', $exams->pluck('id'))
            ->get()
            ->keyBy('online_exam_id');

        return view('tenant.student.exams.index', compact('exams', 'attempts'));
    }

    public function show(OnlineExam $exam)
    {
        $student = Auth::user();

        if (!$this->resolveStudentClassIds($student)->contains($exam->class_id)) {
            abort(403, 'You are not enrolled in this class.');
        }

        $attempt = OnlineExamAttempt::where('online_exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        return view('tenant.student.exams.show', compact('exam', 'attempt'));
    }

    public function start(OnlineExam $exam)
    {
        $student = Auth::user();

        if (!$this->resolveStudentClassIds($student)->contains($exam->class_id)) {
            abort(403, 'You are not enrolled in this class.');
        }

        // Check dates
        $now = now();
        if ($now->lt($exam->starts_at)) {
            return back()->with('error', 'Exam has not started yet.');
        }
        if ($now->gt($exam->ends_at)) {
            return back()->with('error', 'Exam has ended.');
        }

        // Check existing attempt
        $attempt = OnlineExamAttempt::where('online_exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        if ($attempt) {
            if ($attempt->completed_at) {
                return redirect()->route('tenant.student.exams.show', $exam)
                    ->with('info', 'You have already completed this exam.');
            }
            // Resume existing attempt
            return redirect()->route('tenant.student.exams.take', $exam);
        }

        // Create new attempt
        $attempt = OnlineExamAttempt::create([
            'online_exam_id' => $exam->id,
            'student_id' => $student->id,
            'started_at' => $now,
            'status' => 'in_progress',
        ]);

        return redirect()->route('tenant.student.exams.take', $exam);
    }

    public function take(OnlineExam $exam)
    {
        $student = Auth::user();

        if (!$this->resolveStudentClassIds($student)->contains($exam->class_id)) {
            abort(403, 'You are not enrolled in this class.');
        }

        $attempt = OnlineExamAttempt::where('online_exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        if ($attempt->completed_at) {
            return redirect()->route('tenant.student.exams.show', $exam)
                ->with('info', 'You have already completed this exam.');
        }

        // Check if time is up
        $now = now();
        if ($now->gt($exam->ends_at)) {
            // Auto submit
            $attempt->update(['completed_at' => $now, 'status' => 'completed']);
            return redirect()->route('tenant.student.exams.show', $exam)
                ->with('warning', 'Exam time has ended. Your answers have been submitted.');
        }

        // Load questions
        $exam->load(['sections.questions' => function($q) {
            $q->orderBy('order');
        }]);

        return view('tenant.student.exams.take', compact('exam', 'attempt'));
    }

    public function submit(Request $request, OnlineExam $exam)
    {
        $student = Auth::user();

        if (!$this->resolveStudentClassIds($student)->contains($exam->class_id)) {
            abort(403, 'You are not enrolled in this class.');
        }

        $attempt = OnlineExamAttempt::where('online_exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        if ($attempt->completed_at) {
            return redirect()->route('tenant.student.exams.show', $exam);
        }

        // Save answers
        $answers = $request->input('answers', []);
        // Logic to save answers to database would go here
        // For now, we'll just mark as completed and calculate a dummy score if auto-graded
        
        // In a real implementation, we would iterate through questions, compare answers, and calculate score
        // $score = ...;

        $attempt->update([
            'completed_at' => now(),
            'status' => 'completed',
            // 'score' => $score,
        ]);

        return redirect()->route('tenant.student.exams.show', $exam)
            ->with('success', 'Exam submitted successfully.');
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

        return $this->classCache[$student->id] = $classIds;
    }
}
