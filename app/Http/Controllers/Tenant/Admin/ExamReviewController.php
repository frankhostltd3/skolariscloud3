<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnlineExam;
use App\Notifications\ExamReviewDecisionNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = OnlineExam::with(['teacher:id,name,email', 'class:id,name', 'subject:id,name'])
            ->latest('submitted_for_review_at');

        if ($request->filled('status')) {
            $query->where('approval_status', $request->input('status'));
        } else {
            $query->whereIn('approval_status', ['pending_review', 'changes_requested']);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                    ->orWhereHas('teacher', function ($teacher) use ($search) {
                        $teacher->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $exams = $query->paginate(20)->appends($request->query());

        $stats = [
            'pending' => OnlineExam::where('approval_status', 'pending_review')->count(),
            'changes' => OnlineExam::where('approval_status', 'changes_requested')->count(),
            'approved' => OnlineExam::where('approval_status', 'approved')->count(),
            'rejected' => OnlineExam::where('approval_status', 'rejected')->count(),
        ];

        return view('tenant.admin.exams.index', [
            'exams' => $exams,
            'stats' => $stats,
            'activeStatus' => $request->input('status'),
            'search' => $request->input('search'),
        ]);
    }

    public function show(OnlineExam $exam)
    {
        $exam->load([
            'teacher:id,name,email',
            'class:id,name',
            'subject:id,name',
            'sections.questions' => function ($query) {
                $query->orderBy('order');
            },
        ]);

        return view('tenant.admin.exams.show', compact('exam'));
    }

    public function approve(Request $request, OnlineExam $exam): RedirectResponse
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($exam->approval_status === 'approved') {
            return back()->with('info', __('This exam is already approved.'));
        }

        if ($exam->approval_status === 'rejected') {
            return back()->withErrors(['exam' => __('This exam was previously rejected and cannot be approved.')]);
        }

        if ($exam->questions()->count() === 0) {
            return back()->withErrors(['exam' => __('Add questions before approving this exam.')]);
        }

        $exam->approve(Auth::user(), $data['notes'] ?? null);
        $this->applyActivationRules($exam);
        $this->notifyTeacher($exam->fresh(), 'approved', $data['notes'] ?? null);

        return back()->with('success', __('Exam approved successfully.'));
    }

    public function requestChanges(Request $request, OnlineExam $exam): RedirectResponse
    {
        $data = $request->validate([
            'notes' => ['required', 'string', 'max:2000'],
        ]);

        if ($exam->approval_status === 'rejected') {
            return back()->withErrors(['exam' => __('Rejected exams cannot have changes requested. Please ask the teacher to duplicate it instead.')]);
        }

        $exam->requestChanges(Auth::user(), $data['notes']);
        $this->notifyTeacher($exam->fresh(), 'changes_requested', $data['notes']);

        return back()->with('success', __('Teacher notified to make the requested changes.'));
    }

    public function reject(Request $request, OnlineExam $exam): RedirectResponse
    {
        $data = $request->validate([
            'notes' => ['required', 'string', 'max:2000'],
        ]);

        $exam->reject(Auth::user(), $data['notes']);
        $this->notifyTeacher($exam->fresh(), 'rejected', $data['notes']);

        return redirect()
            ->route('admin.exams.index')
            ->with('success', __('Exam rejected and archived.'));
    }

    public function activate(Request $request, OnlineExam $exam): RedirectResponse
    {
        if ($exam->approval_status !== 'approved') {
            return back()->withErrors(['exam' => __('Only approved exams can be activated.')]);
        }

        if ($exam->activation_mode !== 'manual') {
            return back()->withErrors(['exam' => __('This exam auto-activates based on its schedule.')]);
        }

        if ($exam->status === 'active') {
            return back()->with('info', __('Exam is already active.'));
        }

        $exam->forceFill([
            'status' => 'active',
            'activated_at' => now(),
        ])->save();

        $this->notifyTeacher($exam->fresh(), 'activated');

        return back()->with('success', __('Exam activated. Students can now attempt it.'));
    }

    protected function applyActivationRules(OnlineExam $exam): void
    {
        $now = now();

        if ($exam->activation_mode === 'manual') {
            $exam->forceFill([
                'status' => 'scheduled',
                'activated_at' => null,
            ])->save();

            return;
        }

        if ($exam->start_time && $now->lt($exam->start_time)) {
            $exam->forceFill([
                'status' => 'scheduled',
                'activated_at' => null,
            ])->save();

            return;
        }

        $exam->forceFill([
            'status' => 'active',
            'activated_at' => $exam->activated_at ?? $now,
        ])->save();
    }

    protected function notifyTeacher(OnlineExam $exam, string $decision, ?string $notes = null): void
    {
        if (!$exam->teacher) {
            return;
        }

        $exam->teacher->notify(new ExamReviewDecisionNotification($exam, $decision, $notes));
    }
}
