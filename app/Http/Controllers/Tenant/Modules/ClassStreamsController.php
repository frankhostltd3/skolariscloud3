<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use App\Models\ClassStream;
use App\Models\SchoolClass;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClassStreamsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authorize('viewAny', ClassStream::class);
            return $next($request);
        })->only(['index','create']);

        $this->middleware(function ($request, $next) {
            $this->authorize('create', ClassStream::class);
            return $next($request);
        })->only(['store']);
    }
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q'));
        $streams = ClassStream::query()
            ->with('class')
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where('name', 'like', "%$q%")
                   ->orWhereHas('class', function ($cqb) use ($q) {
                       $cqb->where('name', 'like', "%$q%");
                   });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();
        return view('tenant.modules.class_streams.index', compact('streams','q'));
    }
    public function create(): View
    {
        $classes = SchoolClass::query()->orderBy('name')->get(['id','name']);
        return view('tenant.modules.class_streams.create', compact('classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_id' => ['required','exists:classes,id'],
            'name' => ['required','string','max:255'],
        ]);

        ClassStream::create($validated);

        return redirect()->route('tenant.modules.class_streams.index')->with('status', __('Class stream created successfully.'));
    }

    public function show(ClassStream $class_stream): View
    {
        $class_stream->load('class');
        return view('tenant.modules.class_streams.show', compact('class_stream'));
    }

    public function edit(ClassStream $class_stream): View
    {
        $classes = SchoolClass::query()->orderBy('name')->get(['id','name']);
        return view('tenant.modules.class_streams.edit', compact('class_stream','classes'));
    }

    public function update(Request $request, ClassStream $class_stream): RedirectResponse
    {
        $validated = $request->validate([
            'class_id' => ['required','exists:classes,id'],
            'name' => ['required','string','max:255'],
        ]);
        $class_stream->update($validated);
        return redirect()->route('tenant.modules.class_streams.show', $class_stream)->with('status', __('Class stream updated successfully.'));
    }

    public function destroy(ClassStream $class_stream): RedirectResponse
    {
        $class_stream->delete();
        return redirect()->route('tenant.modules.class_streams.index')->with('status', __('Class stream deleted.'));
    }
}
