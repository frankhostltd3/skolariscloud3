<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessExamGeneration;
use App\Models\OnlineExam;
use App\Models\OnlineExamQuestion;
use App\Models\OnlineExamSection;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\ExamSubmittedForReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exams = OnlineExam::with(['class:id,name', 'subject:id,name'])
            ->withCount(['attempts', 'questions'])
            ->byTeacher(Auth::id())
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => OnlineExam::byTeacher(Auth::id())->count(),
            'active' => OnlineExam::byTeacher(Auth::id())->active()->count(),
            'scheduled' => OnlineExam::byTeacher(Auth::id())->scheduled()->count(),
            'completed' => OnlineExam::byTeacher(Auth::id())->completed()->count(),
        ];

        return view('tenant.teacher.classroom.exams.index', compact('exams', 'stats'));
    }

    /**
     * Show the form for creating a new online exam.
     */
    public function create()
    {
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
            'creation_method' => ['required', 'in:manual,automatic,ai'],
            'activation_mode' => ['required', 'in:manual,schedule,auto'],
            'auto_submit_on' => ['required', 'in:time_up,manual,both'],
            'status' => ['nullable', 'in:draft,scheduled,active,completed,archived,cancelled'],
            'max_tab_switches' => ['nullable', 'integer', 'min:0', 'max:20'],
        ]);

        $action = $request->input('action', 'save_draft');
        $status = in_array($request->input('status'), ['draft', 'scheduled'], true)
            ? $request->input('status')
            : 'draft';

        $approvalStatus = 'draft';
        $submittedAt = null;

        if ($action === 'submit_for_review') {
            $approvalStatus = 'pending_review';
            $submittedAt = now();
            $status = 'scheduled';
        }

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
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'status' => $status,
            'grading_method' => $validated['grading_method'],
            'proctored' => $request->boolean('proctored'),
            'disable_copy_paste' => $request->boolean('disable_copy_paste'),
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_answers' => $request->boolean('shuffle_answers'),
            'show_results_immediately' => $request->boolean('show_results_immediately'),
            'allow_backtrack' => $request->boolean('allow_backtrack', true),
            'auto_submit_on' => $validated['auto_submit_on'],
            'max_tab_switches' => $validated['max_tab_switches'] ?? 0,
            'creation_method' => $validated['creation_method'],
            'activation_mode' => $validated['activation_mode'],
            'approval_status' => $approvalStatus,
            'submitted_for_review_at' => $submittedAt,
            'generation_status' => $validated['creation_method'] === 'manual' ? 'idle' : 'requested',
            'generation_provider' => $this->resolveGenerationProvider($validated['creation_method']),
            'generation_metadata' => [],
        ];

        $exam = OnlineExam::create($data);

        if ($approvalStatus === 'pending_review') {
            $this->notifyAdminsOfSubmission($exam);
        }

        return redirect()
            ->route('tenant.teacher.classroom.exams.show', $exam)
            ->with('success', $approvalStatus === 'pending_review'
                ? 'Exam submitted for review. You will be notified after admin approval.'
                : 'Exam saved as draft. You can add sections and questions.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OnlineExam $exam)
    {
        $this->authorize('view', $exam);

        $exam->load(['class', 'subject', 'sections.questions']);

        $stats = [
            'total_students' => $exam->class->students()->count(),
            'attempts' => $exam->attempts()->count(),
            'avg_score' => $exam->getAverageScore(),
            'pass_rate' => $exam->getPassRate(),
            'pending_grading' => $exam->getPendingGradingCount(),
        ];

        return view('tenant.teacher.classroom.exams.show', compact('exam', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OnlineExam $exam)
    {
        $this->authorize('update', $exam);

        $classes = SchoolClass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('tenant.teacher.classroom.exams.edit', compact('exam', 'classes', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OnlineExam $exam)
    {
        $this->authorize('update', $exam);

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
            'creation_method' => ['required', 'in:manual,automatic,ai'],
            'activation_mode' => ['required', 'in:manual,schedule,auto'],
            'auto_submit_on' => ['required', 'in:time_up,manual,both'],
            'status' => ['nullable', 'in:draft,scheduled,active,completed,archived,cancelled'],
            'max_tab_switches' => ['nullable', 'integer', 'min:0', 'max:20'],
        ]);

        $action = $request->input('action', 'save_draft');
        $status = in_array($request->input('status'), ['draft', 'scheduled', 'active'], true)
            ? $request->input('status')
            : $exam->status;

        $approvalStatus = $exam->approval_status;
        $submittedAt = $exam->submitted_for_review_at;

        if ($action === 'submit_for_review') {
            $approvalStatus = 'pending_review';
            $submittedAt = now();
            $status = $status === 'active' ? 'active' : 'scheduled';
        } elseif ($action === 'save_draft') {
            $approvalStatus = $approvalStatus === 'pending_review' ? 'pending_review' : 'draft';
        }

        $data = [
            'class_id' => $validated['class_id'],
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'duration_minutes' => $validated['duration_minutes'],
            'total_marks' => $validated['total_marks'],
            'pass_marks' => $validated['pass_marks'] ?? null,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'status' => $status,
            'grading_method' => $validated['grading_method'],
            'proctored' => $request->boolean('proctored'),
            'disable_copy_paste' => $request->boolean('disable_copy_paste'),
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_answers' => $request->boolean('shuffle_answers'),
            'show_results_immediately' => $request->boolean('show_results_immediately'),
            'allow_backtrack' => $request->boolean('allow_backtrack', true),
            'auto_submit_on' => $validated['auto_submit_on'],
            'max_tab_switches' => $validated['max_tab_switches'] ?? 0,
            'creation_method' => $validated['creation_method'],
            'activation_mode' => $validated['activation_mode'],
            'approval_status' => $approvalStatus,
            'submitted_for_review_at' => $submittedAt,
            'generation_status' => $validated['creation_method'] === 'manual'
                ? 'idle'
                : ($exam->generation_status === 'completed' ? 'completed' : 'requested'),
            'generation_provider' => $this->resolveGenerationProvider($validated['creation_method']),
        ];

        $exam->update($data);

        if ($action === 'submit_for_review') {
            $this->notifyAdminsOfSubmission($exam);
        }

        return redirect()
            ->route('tenant.teacher.classroom.exams.show', $exam)
            ->with('success', $action === 'submit_for_review'
                ? 'Exam submitted for review. We will notify you once it is approved.'
                : 'Exam updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OnlineExam $exam)
    {
        $this->authorize('delete', $exam);

        if ($exam->attempts()->count() > 0) {
            return back()->with('error', 'Cannot delete exam with existing attempts. Please archive it instead.');
        }

        $exam->delete();

        return redirect()->route('tenant.teacher.classroom.exams.index')
            ->with('success', 'Exam deleted successfully.');
    }


    /**
     * Queue AI/automatic generation for the exam content.
     */
    public function generate(Request $request, OnlineExam $exam)
    {
        $this->authorize('update', $exam);

        if ($exam->creation_method === 'manual') {
            return back()->withErrors(['generation' => __('Generation only applies to automatic or AI exams.')]);
        }

        if (in_array($exam->generation_status, ['requested', 'processing'], true)) {
            return back()->withErrors(['generation' => __('Generation is already in progress for this exam.')]);
        }

        $data = $request->validate([
            'syllabus_topics' => ['nullable', 'string', 'max:2000'],
            'learning_objectives' => ['nullable', 'string', 'max:2000'],
            'difficulty' => ['nullable', 'in:foundation,balanced,advanced'],
            'question_types' => ['nullable', 'array'],
            'question_types.*' => ['in:multiple_choice,true_false,short_answer,essay,fill_blank'],
        ]);

        $questionTypes = array_values(array_unique($data['question_types'] ?? []));

        $metadata = $exam->generation_metadata ?? [];
        $metadata['last_request'] = [
            'requested_by' => Auth::id(),
            'requested_at' => now()->toIso8601String(),
            'syllabus_topics' => $data['syllabus_topics'] ?? null,
            'learning_objectives' => $data['learning_objectives'] ?? null,
            'difficulty' => $data['difficulty'] ?? 'balanced',
            'question_types' => $questionTypes,
        ];

        $exam->forceFill([
            'generation_status' => 'requested',
            'generation_metadata' => $metadata,
        ])->save();

        ProcessExamGeneration::dispatch($exam->id, $metadata['last_request']);

        return back()->with('success', __('Generation queued. You will receive a notification when the blueprint is ready.'));
    }

    /**
     * Publish the exam.
     */
    public function publish(OnlineExam $exam)
    {
        $this->authorize('update', $exam);

        if ($exam->questions()->count() === 0) {
            return back()->with('error', 'Cannot submit exam without questions.');
        }

        $exam->markPendingReview();
        $this->notifyAdminsOfSubmission($exam);

        return back()->with('success', 'Exam submitted for review.');
    }

    /**
     * Store a newly created section in storage.
     */
    public function storeSection(Request $request, OnlineExam $exam)
    {
        $this->authorize('update', $exam);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $exam->sections()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'order' => $exam->sections()->max('order') + 1,
        ]);

        return back()->with('success', 'Section added successfully.');
    }

    /**
     * Update the specified section in storage.
     */
    public function updateSection(Request $request, OnlineExam $exam, OnlineExamSection $section)
    {
        $this->authorize('update', $exam);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $section->update($validated);

        return back()->with('success', 'Section updated successfully.');
    }

    /**
     * Remove the specified section from storage.
     */
    public function destroySection(OnlineExam $exam, OnlineExamSection $section)
    {
        $this->authorize('update', $exam);

        if ($section->questions()->count() > 0) {
            return back()->with('error', 'Cannot delete section with questions. Please delete questions first.');
        }

        $section->delete();

        return back()->with('success', 'Section deleted successfully.');
    }

    /**
     * Store a newly created question in storage.
     */
    public function storeQuestion(Request $request, OnlineExam $exam, OnlineExamSection $section)
    {
        $this->authorize('update', $exam);

        $validated = $request->validate([
            'type' => ['required', 'in:multiple_choice,true_false,short_answer,essay,fill_blank'],
            'question_text' => ['required', 'string'],
            'marks' => ['required', 'integer', 'min:1'],
            'options' => ['nullable', 'array'],
            'correct_answer' => ['nullable'], // Validation depends on type
            'explanation' => ['nullable', 'string'],
        ]);

        // Custom validation for correct answer based on type
        if (in_array($validated['type'], ['multiple_choice', 'true_false'])) {
            if (empty($request->input('correct_answer'))) {
                return back()->withErrors(['correct_answer' => 'Correct answer is required for this question type.']);
            }
        }

        $section->questions()->create([
            'online_exam_id' => $exam->id,
            'type' => $validated['type'],
            'question' => $validated['question_text'],
            'marks' => $validated['marks'],
            'options' => $validated['options'] ?? null,
            'correct_answer' => $request->input('correct_answer'), // Can be string or array
            'explanation' => $validated['explanation'] ?? null,
            'order' => $section->questions()->max('order') + 1,
        ]);

        return back()->with('success', 'Question added successfully.');
    }

    /**
     * Update the specified question in storage.
     */
    public function updateQuestion(Request $request, OnlineExam $exam, OnlineExamSection $section, OnlineExamQuestion $question)
    {
        $this->authorize('update', $exam);

        $validated = $request->validate([
            'type' => ['required', 'in:multiple_choice,true_false,short_answer,essay,fill_blank'],
            'question_text' => ['required', 'string'],
            'marks' => ['required', 'integer', 'min:1'],
            'options' => ['nullable', 'array'],
            'correct_answer' => ['nullable'],
            'explanation' => ['nullable', 'string'],
        ]);

        $question->update([
            'type' => $validated['type'],
            'question' => $validated['question_text'],
            'marks' => $validated['marks'],
            'options' => $validated['options'] ?? null,
            'correct_answer' => $request->input('correct_answer'),
            'explanation' => $validated['explanation'] ?? null,
        ]);

        return back()->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroyQuestion(OnlineExam $exam, OnlineExamSection $section, OnlineExamQuestion $question)
    {
        $this->authorize('update', $exam);

        $question->delete();

        return back()->with('success', 'Question deleted successfully.');
    }

    protected function notifyAdminsOfSubmission(OnlineExam $exam): void
    {
        $recipients = User::query()
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'admin', 'super-admin', 'Super Admin']);
            })
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new ExamSubmittedForReviewNotification($exam));
    }

    protected function resolveGenerationProvider(string $method): ?string
    {
        return match ($method) {
            'automatic' => 'blueprint',
            'ai' => config('services.exam_generation.driver', 'ai-bridge'),
            default => null,
        };
    }

}
