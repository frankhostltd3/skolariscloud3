<?php

namespace App\Http\Controllers\Tenant\Academics;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\EducationLevel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authorize('viewAny', Subject::class);
            return $next($request);
        })->only(['index','create']);

        $this->middleware(function ($request, $next) {
            $this->authorize('create', Subject::class);
            return $next($request);
        })->only(['store']);

        $this->middleware(function ($request, $next) {
            $subject = $request->route('subject');
            if ($subject) {
                $this->authorize('update', $subject);
            }
            return $next($request);
        })->only(['edit','update']);

        $this->middleware(function ($request, $next) {
            $subject = $request->route('subject');
            if ($subject) {
                $this->authorize('delete', $subject);
            }
            return $next($request);
        })->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q'));
        $levelId = (int) $request->get('education_level_id');
        $filterByBody = (bool) $request->get('filter_current_body');
        $currentBodyCountry = null;
        if ($filterByBody) {
            $current = \App\Models\ExaminationBody::where('is_current', true)->first();
            $currentBodyCountry = $current?->country;
        }
        $items = Subject::query()->with('educationLevel')
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%$q%")
                      ->orWhere('code', 'like', "%$q%");
                });
            })
            ->when($levelId, fn($qb) => $qb->where('education_level_id', $levelId))
            ->when($filterByBody && $currentBodyCountry, function ($qb) use ($currentBodyCountry) {
                $qb->whereHas('educationLevel', function ($q) use ($currentBodyCountry) {
                    $q->whereNotNull('country')->whereRaw('LOWER(country) = ?', [strtolower($currentBodyCountry)]);
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
        $levels = EducationLevel::orderBy('order')->orderBy('name')->pluck('name','id');
        return view('tenant.academics.subjects.index', compact('items','q','levels','levelId'));
    }

    public function create(): View
    {
        $levels = EducationLevel::orderBy('order')->orderBy('name')->pluck('name','id');
        return view('tenant.academics.subjects.create', compact('levels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required','string','max:50','unique:subjects,code'],
            'name' => ['required','string','max:255'],
            'education_level_id' => ['nullable','exists:education_levels,id'],
        ]);

        Subject::create($validated);

        return redirect()->route('tenant.academics.subjects.index')->with('success', __('Subject created successfully.'));
    }

    public function show(Subject $subject): View
    {
        return view('tenant.academics.subjects.show', ['item' => $subject]);
    }

    public function edit(Subject $subject): View
    {
        $levels = EducationLevel::orderBy('order')->orderBy('name')->pluck('name','id');
        return view('tenant.academics.subjects.edit', ['item' => $subject, 'levels' => $levels]);
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required','string','max:50','unique:subjects,code,'.$subject->id],
            'name' => ['required','string','max:255'],
            'education_level_id' => ['nullable','exists:education_levels,id'],
        ]);
        $subject->update($validated);
        return redirect()->route('tenant.academics.subjects.show', $subject)->with('success', __('Subject updated successfully.'));
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();
        return redirect()->route('tenant.academics.subjects.index')->with('success', __('Subject deleted.'));
    }
}
