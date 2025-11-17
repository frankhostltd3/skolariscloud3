<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use App\Models\LessonPlan;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lessonPlans = LessonPlan::with(['class', 'subject'])
            ->where('teacher_id', Auth::id())
            ->latest('lesson_date')
            ->paginate(15);

        return view('tenant.teacher.classroom.lessons.index', compact('lessonPlans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    $classes = SchoolClass::orderBy('name')->get();
    $subjects = Subject::orderBy('name')->get();

        return view('tenant.teacher.classroom.lessons.create', compact('classes', 'subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'lesson_date' => 'required|date',
            'objectives' => 'nullable|array',
            'materials_needed' => 'nullable|array',
            'introduction' => 'nullable|string',
            'main_content' => 'nullable|string',
            'activities' => 'nullable|array',
            'assessment' => 'nullable|string',
            'homework' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,published,completed',
            'is_template' => 'nullable|boolean',
        ]);

        $validated['teacher_id'] = Auth::id();
        $validated['is_template'] = $request->has('is_template');

        $lessonPlan = LessonPlan::create($validated);

        return redirect()
            ->route('tenant.teacher.classroom.lessons.show', $lessonPlan)
            ->with('success', 'Lesson plan created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
