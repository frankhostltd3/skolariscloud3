<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Teacher\StoreLessonPlanRequest;
use App\Http\Requests\Tenant\Teacher\UpdateLessonPlanRequest;
use App\Models\LessonPlan;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LessonPlanController extends Controller
{
    public function index(Request $request): Renderable
    {
        $teacher = Auth::user();
        $connection = $this->connectionForTeacher();

        if (! tenant_table_exists('lesson_plans', $connection)) {
            $lessonPlans = $this->emptyPaginator($request);

            return view('tenant.teacher.classroom.lessons.index', [
                'lessonPlans' => $lessonPlans,
                'lessonPlansAvailable' => false,
                'statusCounts' => [],
                'reviewCounts' => [],
                'filters' => $this->defaultFilters(),
                'classes' => collect(),
                'subjects' => collect(),
            ]);
        }

        $filters = $this->extractFilters($request);

        $query = LessonPlan::with(['class', 'subject'])
            ->where('teacher_id', $teacher->id);

        if ($filters['search']) {
            $query->where(function ($q) use ($filters): void {
                $q->where('title', 'like', '%'.$filters['search'].'%')
                    ->orWhere('notes', 'like', '%'.$filters['search'].'%');
            });
        }

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['review_status']) {
            $query->where('review_status', $filters['review_status']);
        }

        if ($filters['class_id']) {
            $query->where('class_id', $filters['class_id']);
        }

        if ($filters['subject_id']) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if ($filters['date_from']) {
            $query->whereDate('lesson_date', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $query->whereDate('lesson_date', '<=', $filters['date_to']);
        }

        $lessonPlans = $query
            ->orderByDesc('lesson_date')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->appends($request->query());

        $statusCounts = LessonPlan::select('status', DB::raw('COUNT(*) as total'))
            ->where('teacher_id', $teacher->id)
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $reviewCounts = LessonPlan::select('review_status', DB::raw('COUNT(*) as total'))
            ->where('teacher_id', $teacher->id)
            ->groupBy('review_status')
            ->pluck('total', 'review_status')
            ->toArray();

        $classes = tenant_table_exists('classes', $connection)
            ? SchoolClass::orderBy('name')->get(['id', 'name'])
            : collect();

        $subjects = tenant_table_exists('subjects', $connection)
            ? Subject::orderBy('name')->get(['id', 'name'])
            : collect();

        return view('tenant.teacher.classroom.lessons.index', [
            'lessonPlans' => $lessonPlans,
            'lessonPlansAvailable' => true,
            'statusCounts' => $statusCounts,
            'reviewCounts' => $reviewCounts,
            'filters' => $filters,
            'classes' => $classes,
            'subjects' => $subjects,
        ]);
    }

    public function create(): Renderable|RedirectResponse
    {
        $connection = $this->connectionForTeacher();

        if (! tenant_table_exists('lesson_plans', $connection)) {
            return redirect()
                ->route('tenant.teacher.classroom.lessons.index')
                ->with('warning', __('Lesson plans are not enabled for this school yet.'));
        }

        $classes = tenant_table_exists('classes', $connection)
            ? SchoolClass::orderBy('name')->get(['id', 'name'])
            : collect();

        $subjects = tenant_table_exists('subjects', $connection)
            ? Subject::orderBy('name')->get(['id', 'name'])
            : collect();

        if ($classes->isEmpty() || $subjects->isEmpty()) {
            session()->flash('warning', __('Classes or subjects are missing. Lesson plans can be created once both are configured.'));
        }

        $templates = LessonPlan::where('teacher_id', Auth::id())
            ->templates()
            ->latest('updated_at')
            ->take(12)
            ->get(['id', 'title', 'subject_id', 'updated_at']);

        return view('tenant.teacher.classroom.lessons.create', [
            'classes' => $classes,
            'subjects' => $subjects,
            'templates' => $templates,
        ]);
    }

    public function store(StoreLessonPlanRequest $request): RedirectResponse
    {
        $teacher = Auth::user();
        $connection = $this->connectionForTeacher();

        if (! tenant_table_exists('lesson_plans', $connection)) {
            return redirect()
                ->route('tenant.teacher.classroom.lessons.index')
                ->with('error', __('Lesson plans cannot be created because the required table is missing.'));
        }

        if (! tenant_table_exists('classes', $connection) || ! tenant_table_exists('subjects', $connection)) {
            return redirect()
                ->route('tenant.teacher.classroom.lessons.index')
                ->with('error', __('Lesson plans cannot be created until classes and subjects are configured.'));
        }

        $payload = $request->validated();
        $payload['teacher_id'] = $teacher->id;
        $payload['requires_revision'] = false;
        $action = $request->input('action', 'save_draft');

        [$attributes, $message] = $this->applyWorkflow($payload, $action);

        $lessonPlan = LessonPlan::create($attributes);

        return redirect()
            ->route('tenant.teacher.classroom.lessons.show', $lessonPlan)
            ->with('success', $message);
    }

    public function show(LessonPlan $lesson): Renderable|RedirectResponse
    {
        $teacher = Auth::user();
        $connection = $this->connectionForTeacher();

        if (! tenant_table_exists('lesson_plans', $connection)) {
            return redirect()->route('tenant.teacher.classroom.lessons.index')
                ->with('warning', __('Lesson plan records are not available for this school yet.'));
        }

        $this->ensureOwnership($lesson, $teacher->id);

        $lesson->load(['class', 'subject', 'teacher', 'reviewer']);

        $recentPlans = LessonPlan::where('teacher_id', $teacher->id)
            ->where('id', '!=', $lesson->id)
            ->latest('lesson_date')
            ->limit(5)
            ->get(['id', 'title', 'lesson_date', 'status', 'review_status']);

        return view('tenant.teacher.classroom.lessons.show', [
            'lessonPlan' => $lesson,
            'recentPlans' => $recentPlans,
        ]);
    }

    public function edit(LessonPlan $lesson): Renderable|RedirectResponse
    {
        $teacher = Auth::user();
        $connection = $this->connectionForTeacher();

        if (! tenant_table_exists('lesson_plans', $connection)) {
            return redirect()->route('tenant.teacher.classroom.lessons.index')
                ->with('warning', __('Lesson plan records are not available for this school yet.'));
        }

        $this->ensureOwnership($lesson, $teacher->id);

        if (! $lesson->isEditable()) {
            return redirect()->route('tenant.teacher.classroom.lessons.show', $lesson)
                ->with('warning', __('Approved or archived lesson plans cannot be edited. Consider duplicating this plan instead.'));
        }

        $classes = tenant_table_exists('classes', $connection)
            ? SchoolClass::orderBy('name')->get(['id', 'name'])
            : collect();

        $subjects = tenant_table_exists('subjects', $connection)
            ? Subject::orderBy('name')->get(['id', 'name'])
            : collect();

        $templates = LessonPlan::where('teacher_id', $teacher->id)
            ->templates()
            ->latest('updated_at')
            ->take(10)
            ->get(['id', 'title', 'subject_id', 'updated_at']);

        return view('tenant.teacher.classroom.lessons.edit', [
            'lessonPlan' => $lesson,
            'classes' => $classes,
            'subjects' => $subjects,
            'templates' => $templates,
        ]);
    }

    public function update(UpdateLessonPlanRequest $request, LessonPlan $lesson): RedirectResponse
    {
        $teacher = Auth::user();
        $connection = $this->connectionForTeacher();

        if (! tenant_table_exists('lesson_plans', $connection)) {
            return redirect()->route('tenant.teacher.classroom.lessons.index')
                ->with('warning', __('Lesson plan records are not available for this school yet.'));
        }

        $this->ensureOwnership($lesson, $teacher->id);

        if (! $lesson->isEditable()) {
            return redirect()->route('tenant.teacher.classroom.lessons.show', $lesson)
                ->with('warning', __('Approved or archived lesson plans cannot be edited. Consider duplicating this plan instead.'));
        }

        $payload = $request->validated();
        $payload['teacher_id'] = $teacher->id;
        $action = $request->input('action', 'save_draft');

        if ($action === 'resubmit' && ! $lesson->canResubmit()) {
            $action = 'submit';
        }

        [$attributes, $message] = $this->applyWorkflow($payload, $action, $lesson);

        $lesson->update($attributes);

        return redirect()
            ->route('tenant.teacher.classroom.lessons.show', $lesson)
            ->with('success', $message);
    }

    public function destroy(LessonPlan $lesson): RedirectResponse
    {
        $teacher = Auth::user();
        $this->ensureOwnership($lesson, $teacher->id);

        if ($lesson->review_status === LessonPlan::REVIEW_APPROVED) {
            return redirect()->route('tenant.teacher.classroom.lessons.show', $lesson)
                ->with('error', __('Approved lesson plans cannot be deleted. Archive the plan if it should no longer appear.'));
        }

        if ($lesson->status === LessonPlan::STATUS_COMPLETED) {
            return redirect()->route('tenant.teacher.classroom.lessons.show', $lesson)
                ->with('error', __('Delivered lesson plans cannot be deleted. Archive the plan instead.'));
        }

        $lesson->delete();

        return redirect()->route('tenant.teacher.classroom.lessons.index')
            ->with('success', __('Lesson plan deleted successfully.'));
    }

    public function submit(Request $request, LessonPlan $lesson): RedirectResponse
    {
        $teacher = Auth::user();
        $this->ensureOwnership($lesson, $teacher->id);

        if (! $lesson->canSubmit()) {
            return back()->with('warning', __('This plan is already awaiting review or has been finalised.'));
        }

        $lesson->submitForReview();

        return back()->with('success', __('Lesson plan submitted for admin review.'));
    }

    public function markInProgress(LessonPlan $lesson): RedirectResponse
    {
        $teacher = Auth::user();
        $this->ensureOwnership($lesson, $teacher->id);

        if ($lesson->review_status !== LessonPlan::REVIEW_APPROVED) {
            return back()->with('warning', __('Lesson plans should be approved by the admin before being marked as in progress.'));
        }

        $lesson->update(['status' => LessonPlan::STATUS_IN_PROGRESS]);

        return back()->with('success', __('Lesson plan marked as in progress.'));
    }

    public function markCompleted(LessonPlan $lesson): RedirectResponse
    {
        $teacher = Auth::user();
        $this->ensureOwnership($lesson, $teacher->id);

        if (! $lesson->canMarkDelivered()) {
            return back()->with('warning', __('Only approved lesson plans can be marked as completed.'));
        }

        $lesson->markAsCompleted();

        return back()->with('success', __('Lesson plan marked as completed. Great job!'));
    }

    public function duplicate(Request $request, LessonPlan $lesson): RedirectResponse
    {
        $teacher = Auth::user();
        $this->ensureOwnership($lesson, $teacher->id);

        $duplicate = $lesson->replicate();
        $duplicate->fill([
            'status' => LessonPlan::STATUS_DRAFT,
            'review_status' => LessonPlan::REVIEW_NOT_SUBMITTED,
            'lesson_date' => $request->input('lesson_date') ?: null,
            'submitted_at' => null,
            'reviewed_at' => null,
            'approved_at' => null,
            'reviewed_by' => null,
            'review_feedback' => null,
            'requires_revision' => false,
            'delivered_at' => null,
            'archived_at' => null,
            'is_template' => false,
            'title' => Str::of($lesson->title)->append(' (Copy)')->value(),
        ]);
        $duplicate->teacher_id = $teacher->id;
        $duplicate->save();

        return redirect()->route('tenant.teacher.classroom.lessons.edit', $duplicate)
            ->with('success', __('Lesson plan duplicated. Update the details and submit when ready.'));
    }

    protected function applyWorkflow(array $attributes, string $action, ?LessonPlan $existing = null): array
    {
        $message = __('Lesson plan saved successfully.');
        $now = now();

        $attributes['status'] = $attributes['status'] ?? ($existing->status ?? LessonPlan::STATUS_DRAFT);
        $attributes['review_status'] = $attributes['review_status'] ?? ($existing->review_status ?? LessonPlan::REVIEW_NOT_SUBMITTED);
        $attributes['submitted_at'] = $attributes['submitted_at'] ?? $existing?->submitted_at;
        $attributes['delivered_at'] = $attributes['delivered_at'] ?? $existing?->delivered_at;

        switch ($action) {
            case 'submit':
            case 'submit_for_review':
            case 'resubmit':
                $attributes['status'] = $attributes['status'] === LessonPlan::STATUS_COMPLETED
                    ? LessonPlan::STATUS_COMPLETED
                    : LessonPlan::STATUS_SCHEDULED;
                $attributes['review_status'] = LessonPlan::REVIEW_PENDING;
                $attributes['submitted_at'] = $now;
                $attributes['reviewed_by'] = null;
                $attributes['reviewed_at'] = null;
                $attributes['approved_at'] = null;
                $attributes['requires_revision'] = false;
                $message = __('Lesson plan submitted for admin review.');
                break;
            default:
                $attributes['status'] = LessonPlan::STATUS_DRAFT;
                if ($existing && $existing->review_status === LessonPlan::REVIEW_REVISION) {
                    $attributes['review_status'] = LessonPlan::REVIEW_REVISION;
                } else {
                    $attributes['review_status'] = LessonPlan::REVIEW_NOT_SUBMITTED;
                    $attributes['submitted_at'] = null;
                }
                $message = __('Lesson plan saved as draft.');
                break;
        }

        if (isset($attributes['start_time'], $attributes['end_time']) && $attributes['start_time'] && $attributes['end_time']) {
            $attributes['duration_minutes'] = $this->calculateDurationMinutes($attributes['start_time'], $attributes['end_time']);
        }

        return [$attributes, $message];
    }

    protected function calculateDurationMinutes($start, $end): ?int
    {
        try {
            $startTime = $start instanceof Carbon ? $start : Carbon::createFromFormat('H:i', (string) $start);
            $endTime = $end instanceof Carbon ? $end : Carbon::createFromFormat('H:i', (string) $end);

            return $endTime->greaterThan($startTime)
                ? $startTime->diffInMinutes($endTime)
                : null;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function ensureOwnership(LessonPlan $lessonPlan, int $teacherId): void
    {
        if ((int) $lessonPlan->teacher_id !== (int) $teacherId) {
            abort(403, __('You are not authorised to manage this lesson plan.'));
        }
    }

    protected function connectionForTeacher(): string
    {
        $teacher = Auth::user();

        return $teacher?->getConnectionName() ?? config('database.default', 'tenant');
    }

    protected function emptyPaginator(Request $request): LengthAwarePaginator
    {
        return new Paginator([], 0, 15, $request->integer('page', 1), [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
    }

    protected function defaultFilters(): array
    {
        return [
            'search' => null,
            'status' => null,
            'review_status' => null,
            'class_id' => null,
            'subject_id' => null,
            'date_from' => null,
            'date_to' => null,
        ];
    }

    protected function extractFilters(Request $request): array
    {
        $filters = $this->defaultFilters();

        foreach ($filters as $key => $value) {
            $filters[$key] = $request->filled($key) ? $request->input($key) : $value;
        }

        if ($filters['date_from']) {
            $filters['date_from'] = Carbon::parse($filters['date_from'])->startOfDay();
        }

        if ($filters['date_to']) {
            $filters['date_to'] = Carbon::parse($filters['date_to'])->endOfDay();
        }

        return $filters;
    }
}
