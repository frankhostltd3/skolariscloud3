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
        $quizzes = Quiz::with(['class', 'subject'])
            ->where('teacher_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('tenant.teacher.classroom.quizzes.index', compact('quizzes'));
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
