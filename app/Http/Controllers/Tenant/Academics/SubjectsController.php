<?php

namespace App\Http\Controllers\Tenant\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use App\Models\EducationLevel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:subjects.view')->only(['index','show']);
        $this->middleware('permission:subjects.create')->only(['create','store']);
        $this->middleware('permission:subjects.edit')->only(['edit','update']);
        $this->middleware('permission:subjects.delete')->only(['destroy']);
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
        $educationLevels = EducationLevel::orderBy('order')->orderBy('name')->get();
        return view('tenant.academics.subjects.create', compact('educationLevels'));
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Subject::create($request->validated());

        return redirect()->route('tenant.academics.subjects.index')->with('success', __('Subject created successfully.'));
    }

    public function show(Subject $subject): View
    {
        return view('tenant.academics.subjects.show', ['item' => $subject]);
    }

    public function edit(Subject $subject): View
    {
        $educationLevels = EducationLevel::orderBy('order')->orderBy('name')->get();
        return view('tenant.academics.subjects.edit', ['item' => $subject, 'educationLevels' => $educationLevels]);
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());
        return redirect()->route('tenant.academics.subjects.show', $subject)->with('success', __('Subject updated successfully.'));
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();
        return redirect()->route('tenant.academics.subjects.index')->with('success', __('Subject deleted.'));
    }
}
