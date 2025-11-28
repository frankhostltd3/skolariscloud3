<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonPlan;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LessonPlanReviewController extends Controller
{
    public function index(Request $request): Renderable
    {
        $connection = $this->tenantConnection();

        if (! tenant_table_exists('lesson_plans', $connection)) {
            return view('tenant.admin.lesson-plans.index', [
                'lessonPlansAvailable' => false,
                'lessonPlans' => collect(),
                'filters' => $this->defaultFilters(),
                'statusCounts' => [],
                'reviewCounts' => [],
                'classes' => collect(),
                'subjects' => collect(),
                'teachers' => collect(),
                'statusOptions' => LessonPlan::statusOptions(),
                'reviewOptions' => LessonPlan::reviewStatusOptions(),
            ]);
        }

        $filters = $this->extractFilters($request);

        $lessonPlansQuery = LessonPlan::with(['teacher', 'class', 'subject'])
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', '%'.$search.'%')
                        ->orWhere('notes', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['review_status'], fn ($query, $review) => $query->where('review_status', $review))
            ->when($filters['class_id'], fn ($query, $classId) => $query->where('class_id', $classId))
            ->when($filters['subject_id'], fn ($query, $subjectId) => $query->where('subject_id', $subjectId))
            ->when($filters['teacher_id'], fn ($query, $teacherId) => $query->where('teacher_id', $teacherId))
            ->when($filters['date_from'], fn ($query, $from) => $query->whereDate('lesson_date', '>=', $from))
            ->when($filters['date_to'], fn ($query, $to) => $query->whereDate('lesson_date', '<=', $to))
            ->orderByDesc('submitted_at')
            ->orderByDesc('updated_at');

        $lessonPlans = $lessonPlansQuery
            ->paginate(20)
            ->appends($request->query());

        $statusCounts = LessonPlan::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $reviewCounts = LessonPlan::select('review_status', DB::raw('COUNT(*) as total'))
            ->groupBy('review_status')
            ->pluck('total', 'review_status')
            ->toArray();

        $classes = tenant_table_exists('classes', $connection)
            ? SchoolClass::orderBy('name')->get(['id', 'name'])
            : collect();

        $subjects = tenant_table_exists('subjects', $connection)
            ? Subject::orderBy('name')->get(['id', 'name'])
            : collect();

        $teachers = tenant_table_exists('users', $connection)
            ? User::role('Teacher')->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('tenant.admin.lesson-plans.index', [
            'lessonPlansAvailable' => true,
            'lessonPlans' => $lessonPlans,
            'filters' => $filters,
            'statusCounts' => $statusCounts,
            'reviewCounts' => $reviewCounts,
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'statusOptions' => LessonPlan::statusOptions(),
            'reviewOptions' => LessonPlan::reviewStatusOptions(),
        ]);
    }

    public function show(LessonPlan $lessonPlan): Renderable|RedirectResponse
    {
        $connection = $this->tenantConnection();

        if (! tenant_table_exists('lesson_plans', $connection)) {
            return redirect()->route('tenant.admin.lesson-plans.index')
                ->with('warning', __('Lesson plans are not available for this school yet.'));
        }

        $lessonPlan->load(['teacher', 'class', 'subject']);

        $recentPlans = LessonPlan::where('teacher_id', $lessonPlan->teacher_id)
            ->where('id', '!=', $lessonPlan->id)
            ->latest('lesson_date')
            ->limit(5)
            ->get(['id', 'title', 'lesson_date', 'status', 'review_status']);

        return view('tenant.admin.lesson-plans.show', [
            'lessonPlan' => $lessonPlan,
            'recentPlans' => $recentPlans,
        ]);
    }

    public function approve(Request $request, LessonPlan $lessonPlan): RedirectResponse
    {
        $this->guardLessonPlanAvailability();

        $data = $request->validate([
            'feedback' => ['nullable', 'string', 'max:2000'],
        ]);

        $lessonPlan->approve($data['feedback'] ?? null, Auth::id());

        return back()->with('success', __('Lesson plan approved successfully.'));
    }

    public function requestRevision(Request $request, LessonPlan $lessonPlan): RedirectResponse
    {
        $this->guardLessonPlanAvailability();

        $data = $request->validate([
            'feedback' => ['required', 'string', 'max:2000'],
        ]);

        $lessonPlan->requestRevision($data['feedback'], Auth::id());

        return back()->with('success', __('Revision requested from teacher.'));
    }

    public function reject(Request $request, LessonPlan $lessonPlan): RedirectResponse
    {
        $this->guardLessonPlanAvailability();

        $data = $request->validate([
            'feedback' => ['required', 'string', 'max:2000'],
        ]);

        $lessonPlan->reject($data['feedback'], Auth::id());

        return back()->with('success', __('Lesson plan rejected. Teacher has been notified.'));
    }

    public function reopen(LessonPlan $lessonPlan): RedirectResponse
    {
        $this->guardLessonPlanAvailability();

        $lessonPlan->reopen();

        return back()->with('success', __('Lesson plan review reset.'));
    }

    protected function tenantConnection(): string
    {
        if (app()->bound('currentSchool')) {
            return 'tenant';
        }

        return config('database.default', 'tenant');
    }

    protected function guardLessonPlanAvailability(): void
    {
        if (! tenant_table_exists('lesson_plans', $this->tenantConnection())) {
            abort(404, __('Lesson plans are not available for this school.'));
        }
    }

    protected function defaultFilters(): array
    {
        return [
            'search' => null,
            'status' => null,
            'review_status' => null,
            'class_id' => null,
            'subject_id' => null,
            'teacher_id' => null,
            'date_from' => null,
            'date_to' => null,
        ];
    }

    protected function extractFilters(Request $request): array
    {
        $filters = $this->defaultFilters();

        foreach (array_keys($filters) as $key) {
            $filters[$key] = $request->filled($key) ? $request->input($key) : null;
        }

        return $filters;
    }
}
