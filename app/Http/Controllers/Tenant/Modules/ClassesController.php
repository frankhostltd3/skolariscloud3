<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClassesController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authorize('viewAny', SchoolClass::class);
            return $next($request);
        })->only(['index','create']);

        $this->middleware(function ($request, $next) {
            $this->authorize('create', SchoolClass::class);
            return $next($request);
        })->only(['store']);
    }
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q'));
        $classes = SchoolClass::query()
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where('name', 'like', "%$q%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
        return view('tenant.modules.classes.index', compact('classes','q'));
    }
    public function create(): View
    {
        return view('tenant.modules.classes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255','unique:classes,name'],
        ]);

        SchoolClass::create($validated);

        return redirect()->route('tenant.modules.classes.index')->with('status', __('Class created successfully.'));
    }

    public function show(SchoolClass $class): View
    {
        return view('tenant.modules.classes.show', ['class' => $class]);
    }

    public function edit(SchoolClass $class): View
    {
        return view('tenant.modules.classes.edit', ['class' => $class]);
    }

    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255','unique:classes,name,'.$class->id],
        ]);
        $class->update($validated);
        return redirect()->route('tenant.modules.classes.show', $class)->with('status', __('Class updated successfully.'));
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $class->delete();
        return redirect()->route('tenant.modules.classes.index')->with('status', __('Class deleted.'));
    }
}
