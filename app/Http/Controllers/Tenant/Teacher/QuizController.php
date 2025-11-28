<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teacherId = Auth::id();
        $now = now();

        // All quizzes for this teacher
        $allQuizzes = Quiz::with(['class', 'subject'])
            ->withCount('questions')
            ->where('teacher_id', $teacherId)
            ->latest()
            ->paginate(15);

        // Active quizzes: published and within the available time window
        $activeQuizzes = Quiz::with(['class', 'subject'])
            ->withCount('questions')
            ->where('teacher_id', $teacherId)
            ->where('status', 'published')
            ->where(function ($query) use ($now) {
                $query->whereNull('available_from')
                    ->orWhere('available_from', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('available_until')
                    ->orWhere('available_until', '>=', $now);
            })
            ->latest()
            ->get();

        // Draft quizzes
        $draftQuizzes = Quiz::with(['class', 'subject'])
            ->withCount('questions')
            ->where('teacher_id', $teacherId)
            ->where('status', 'draft')
            ->latest()
            ->get();

        // Completed quizzes: published but time has expired, or archived
        $completedQuizzes = Quiz::with(['class', 'subject'])
            ->withCount('questions')
            ->where('teacher_id', $teacherId)
            ->where(function ($query) use ($now) {
                $query->where('status', 'archived')
                    ->orWhere(function ($q) use ($now) {
                        $q->where('status', 'published')
                            ->whereNotNull('available_until')
                            ->where('available_until', '<', $now);
                    });
            })
            ->latest()
            ->get();

        // Stats
        $totalQuizzes = Quiz::where('teacher_id', $teacherId)->count();
        $totalQuestions = Quiz::where('teacher_id', $teacherId)
            ->withCount('questions')
            ->get()
            ->sum('questions_count');

        return view('tenant.teacher.classroom.quizzes.index', compact(
            'allQuizzes',
            'activeQuizzes',
            'draftQuizzes',
            'completedQuizzes',
            'totalQuizzes',
            'totalQuestions'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Removed non-existent is_active filters to match current schema
        $classes = SchoolClass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('tenant.teacher.classroom.quizzes.create', compact('classes', 'subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
            'duration_minutes' => 'nullable|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'pass_marks' => 'nullable|integer|min:1',
            'max_attempts' => 'required|integer|min:1|max:10',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_answers' => 'nullable|boolean',
            'show_results_immediately' => 'nullable|boolean',
            'show_correct_answers' => 'nullable|boolean',
            'allow_review' => 'nullable|boolean',
            'status' => 'required|in:draft,published,archived',
        ]);

        $validated['teacher_id'] = Auth::id();
        $validated['shuffle_questions'] = $request->has('shuffle_questions');
        $validated['shuffle_answers'] = $request->has('shuffle_answers');
        $validated['show_results_immediately'] = $request->has('show_results_immediately');
        $validated['show_correct_answers'] = $request->has('show_correct_answers');
        $validated['allow_review'] = $request->has('allow_review');

        $quiz = Quiz::create($validated);

        return redirect()
            ->route('tenant.teacher.classroom.quizzes.show', $quiz)
            ->with('success', 'Quiz created successfully! You can now add questions.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $quiz)
    {
        // Ensure teacher owns this quiz
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $quiz->load(['class', 'subject', 'questions', 'attempts']);

        return view('tenant.teacher.classroom.quizzes.show', compact('quiz'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quiz $quiz)
    {
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $classes = SchoolClass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('tenant.teacher.classroom.quizzes.edit', compact('quiz', 'classes', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
            'duration_minutes' => 'nullable|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'pass_marks' => 'nullable|integer|min:1',
            'max_attempts' => 'required|integer|min:1|max:10',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_answers' => 'nullable|boolean',
            'show_results_immediately' => 'nullable|boolean',
            'show_correct_answers' => 'nullable|boolean',
            'allow_review' => 'nullable|boolean',
            'status' => 'required|in:draft,published,archived',
        ]);

        $validated['shuffle_questions'] = $request->has('shuffle_questions');
        $validated['shuffle_answers'] = $request->has('shuffle_answers');
        $validated['show_results_immediately'] = $request->has('show_results_immediately');
        $validated['show_correct_answers'] = $request->has('show_correct_answers');
        $validated['allow_review'] = $request->has('allow_review');

        $quiz->update($validated);

        return redirect()
            ->route('tenant.teacher.classroom.quizzes.show', $quiz)
            ->with('success', 'Quiz updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $quiz->delete();

        return redirect()
            ->route('tenant.teacher.classroom.quizzes.index')
            ->with('success', 'Quiz deleted successfully!');
    }

    public function addQuestion(Request $request, Quiz $quiz)
    {
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'question' => 'required|string',
            'marks' => 'required|integer|min:1',
            'options' => 'nullable|array',
            'correct_answer' => 'nullable',
            'explanation' => 'nullable|string',
        ]);

        $quiz->questions()->create($validated);

        return back()->with('success', 'Question added successfully!');
    }

    public function deleteQuestion(Quiz $quiz, $questionId)
    {
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $quiz->questions()->where('id', $questionId)->delete();

        return back()->with('success', 'Question deleted successfully!');
    }

    public function updateQuestion(Request $request, Quiz $quiz, $questionId)
    {
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'question' => 'required|string',
            'marks' => 'required|integer|min:1',
            'options' => 'nullable|array',
            'correct_answer' => 'nullable',
            'correct_answer_tf' => 'nullable',
            'explanation' => 'nullable|string',
        ]);

        // Handle true/false correct answer separately
        if ($validated['type'] === 'true_false' && isset($validated['correct_answer_tf'])) {
            $validated['correct_answer'] = $validated['correct_answer_tf'];
        }
        unset($validated['correct_answer_tf']);

        $question = $quiz->questions()->where('id', $questionId)->first();

        if (!$question) {
            abort(404, 'Question not found.');
        }

        $question->update($validated);

        return back()->with('success', 'Question updated successfully!');
    }
}
