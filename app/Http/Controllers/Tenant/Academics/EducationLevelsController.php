<?php

namespace App\Http\Controllers\Tenant\Academics;

use App\Http\Controllers\Controller;
use App\Models\EducationLevel;
use App\Models\GradingScheme;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EducationLevelsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view education levels|manage education levels')->only(['index','show']);
        $this->middleware('permission:manage education levels')->except(['index','show']);
    }
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $items = EducationLevel::query()
            ->when($q !== '', fn($qb) => $qb->where('name','like',"%$q%")
                ->orWhere('code','like',"%$q%")
                ->orWhere('country','like',"%$q%"))
            ->orderBy('order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
        return view('tenant.academics.education_levels.index', compact('items','q'));
    }

    public function create(): View
    {
        $schemes = GradingScheme::orderByDesc('is_current')->orderBy('name')->get(['id','name']);
        return view('tenant.academics.education_levels.create', compact('schemes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'country' => ['nullable','string','max:120'],
            'code' => ['nullable','string','max:50'],
            'order' => ['nullable','integer','min:0','max:255'],
            'grading_scheme_id' => ['nullable','exists:grading_schemes,id'],
        ]);
        EducationLevel::create($data);
        return redirect()->route('tenant.academics.education_levels.index')->with('success', __('Education level created.'));
    }

    public function show(EducationLevel $education_level): View
    {
        return view('tenant.academics.education_levels.show', ['item' => $education_level]);
    }

    public function edit(EducationLevel $education_level): View
    {
        $schemes = GradingScheme::orderByDesc('is_current')->orderBy('name')->get(['id','name']);
        return view('tenant.academics.education_levels.edit', ['item' => $education_level, 'schemes' => $schemes]);
    }

    public function update(Request $request, EducationLevel $education_level): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'country' => ['nullable','string','max:120'],
            'code' => ['nullable','string','max:50'],
            'order' => ['nullable','integer','min:0','max:255'],
            'grading_scheme_id' => ['nullable','exists:grading_schemes,id'],
        ]);
        $education_level->update($data);
        return redirect()->route('tenant.academics.education_levels.show', $education_level)->with('success', __('Education level updated.'));
    }

    public function destroy(EducationLevel $education_level): RedirectResponse
    {
        $subjectCount = $education_level->subjects()->count();
        $reassignTo = request()->input('reassign_to');

        if ($subjectCount > 0 && !$reassignTo) {
            return back()->with('error', __('Cannot delete level with :n subjects. Please reassign subjects to another level first.', ['n' => $subjectCount]));
        }

        if ($reassignTo) {
            $target = EducationLevel::find($reassignTo);
            if (! $target) {
                return back()->with('error', __('Selected reassignment level does not exist.'));
            }
            $education_level->subjects()->update(['education_level_id' => $target->id]);
        }

        $education_level->delete();
        return redirect()->route('tenant.academics.education_levels.index')->with('success', __('Education level deleted.'));
    }
}
