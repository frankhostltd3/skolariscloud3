<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use App\Models\OnlineExam;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    /**
     * Show the form for creating a new online exam.
     */
    public function create()
    {
        // Avoid non-existent schema flags like is_active; order by name for UX
        $classes = SchoolClass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('tenant.teacher.classroom.exams.create', compact('classes', 'subjects'));
    }

    /**
     * Store a newly created online exam.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'total_marks' => ['required', 'integer', 'min:1'],
            'pass_marks' => ['nullable', 'integer', 'min:1'],
            'grading_method' => ['required', 'in:auto,manual,mixed'],
            'status' => ['required', 'in:draft,scheduled,active,completed,archived'],
            'max_tab_switches' => ['nullable', 'integer', 'min:0', 'max:20'],
            'auto_submit_on' => ['nullable', 'in:time_up,manual,both'], // ignored by model for now
        ]);

        // Map fields to OnlineExam schema
        $data = [
            'teacher_id' => Auth::id(),
            'class_id' => $validated['class_id'],
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'duration_minutes' => $validated['duration_minutes'],
            'total_marks' => $validated['total_marks'],
            'pass_marks' => $validated['pass_marks'] ?? null,
            // Derive exam_date from starts_at date part (optional in model)
            'exam_date' => $request->input('starts_at') ? \Illuminate\Support\Carbon::parse($request->input('starts_at'))->toDateString() : null,
            'start_time' => $validated['starts_at'],
            'end_time' => $validated['ends_at'],
            'status' => $validated['status'],
            'grading_method' => $validated['grading_method'],
            // Toggles (checkboxes)
            'proctored' => $request->boolean('proctored'),
            'disable_copy_paste' => $request->boolean('disable_copy_paste'),
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            // View uses shuffle_options; map to shuffle_answers in model
            'shuffle_answers' => $request->boolean('shuffle_options'),
            'show_results_immediately' => $request->boolean('show_results_immediately'),
            // 'allow_backtrack' and 'auto_submit_on' are ignored (not in model schema)
            'max_tab_switches' => $validated['max_tab_switches'] ?? 0,
        ];

        $exam = OnlineExam::create($data);

        return redirect()
            ->route('tenant.teacher.classroom.exams.index')
            ->with('success', 'Exam "' . $exam->title . '" created successfully. You can now add sections and questions.');
    }
}
