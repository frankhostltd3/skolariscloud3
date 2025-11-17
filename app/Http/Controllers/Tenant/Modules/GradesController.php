<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GradesController extends Controller
{
    public function index(): View
    {
        $onlyAligned = (bool) request('only_aligned');
        $currentBody = \App\Models\ExaminationBody::where('is_current', true)->first();

        // For demo purposes, still show placeholder if no Grade data exists
        $recent = [];

        // If we have Grade records, fetch the latest, optionally filtered by alignment
        if (\App\Models\Grade::query()->exists()) {
            $query = \App\Models\Grade::query()
                ->with(['subject.educationLevel'])
                ->latest('awarded_on')
                ->limit(20);

            if ($onlyAligned && $currentBody?->country) {
                $country = strtolower($currentBody->country);
                $query->whereHas('subject.educationLevel', function ($q) use ($country) {
                    $q->whereNotNull('country')->whereRaw('LOWER(country) = ?', [$country]);
                });
            }

            $recent = $query->get()->map(function ($g) {
                return [
                    'id' => $g->id,
                    'course' => optional($g->subject)->name ?? '—',
                    'grade' => (string) $g->score,
                    'band' => $g->band_label ?: $g->band_code ?: '—',
                    'date' => optional($g->awarded_on) ? (string) $g->awarded_on : ($g->updated_at?->toDateString() ?? $g->created_at?->toDateString()),
                ];
            })->all();
        }

        // Fallback placeholders if still empty
        if (empty($recent)) {
            $recent = [
                ['course' => 'Mathematics', 'grade' => 'A', 'band' => 'A', 'date' => now()->subDays(2)->toDateString()],
                ['course' => 'English', 'grade' => 'B+', 'band' => 'B+', 'date' => now()->subDays(5)->toDateString()],
            ];
        }

        return view('tenant.modules.grades.index', compact('recent'));
    }

    public function enter(): View
    {
        $students = Student::orderBy('name')->get(['id','name']);
        $subjects = Subject::orderBy('name')->get(['id','name']);
        $teachers = Teacher::orderBy('name')->get(['id','name']);
        return view('tenant.modules.grades.enter', compact('students','subjects','teachers'));
    }

    public function show(Grade $grade): View
    {
        $grade->load(['student','subject.educationLevel','teacher']);
        return view('tenant.modules.grades.show', compact('grade'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required','exists:students,id'],
            'subject_id' => ['required','exists:subjects,id'],
            'teacher_id' => ['nullable','exists:teachers,id'],
            'term' => ['nullable','string','max:50'],
            'score' => ['required','string','max:10'],
            'awarded_on' => ['nullable','date'],
        ]);

        $bandCode = null;
        $bandLabel = null;

        // Map numeric score to band if applicable
        $numeric = null;
        if (is_numeric($validated['score'])) {
            $numeric = (float) $validated['score'];
            $subject = \App\Models\Subject::with('educationLevel')->find($validated['subject_id']);
            $currentTerm = \App\Models\Term::where('is_current', true)->first();
            $scheme = \App\Support\Grading::resolveScheme($subject, $currentTerm);
            if ($scheme) {
                $band = \App\Support\Grading::mapScoreToBand($scheme, $numeric);
                if ($band) {
                    $bandCode = $band->code;
                    $bandLabel = $band->label;
                }
            }
        }

        // upsert-like behavior for unique(student_id,subject_id,term)
        $grade = Grade::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'subject_id' => $validated['subject_id'],
                'term' => $validated['term'] ?? null,
            ],
            [
                'teacher_id' => $validated['teacher_id'] ?? null,
                'score' => $validated['score'],
                'band_code' => $bandCode,
                'band_label' => $bandLabel,
                'awarded_on' => $validated['awarded_on'] ?? null,
            ]
        );

        return redirect()
            ->route('tenant.modules.grades.enter')
            ->with('status', __('Grade saved successfully.'));
    }
}
