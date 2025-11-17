<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use App\Models\Subject;
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
    }
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q'));
        $subjects = Subject::query()
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where('name', 'like', "%$q%")
                   ->orWhere('code', 'like', "%$q%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
        return view('tenant.modules.subjects.index', compact('subjects','q'));
    }
    public function create(): View
    {
        return view('tenant.modules.subjects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required','string','max:50','unique:subjects,code'],
            'name' => ['required','string','max:255'],
        ]);

        Subject::create($validated);

        return redirect()->route('tenant.modules.subjects.index')->with('status', __('Subject created successfully.'));
    }

    public function show(Subject $subject): View
    {
        return view('tenant.modules.subjects.show', compact('subject'));
    }

    public function edit(Subject $subject): View
    {
        return view('tenant.modules.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required','string','max:50','unique:subjects,code,'.$subject->id],
            'name' => ['required','string','max:255'],
        ]);
        $subject->update($validated);
        return redirect()->route('tenant.modules.subjects.show', $subject)->with('status', __('Subject updated successfully.'));
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();
        return redirect()->route('tenant.modules.subjects.index')->with('status', __('Subject deleted.'));
    }
}
