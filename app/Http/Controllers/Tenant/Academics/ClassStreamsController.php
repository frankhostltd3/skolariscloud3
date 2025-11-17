<?php

namespace App\Http\Controllers\Tenant\Academics;

use App\Http\Controllers\Controller;
use App\Models\ClassStream;
use App\Models\SchoolClass;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClassStreamsController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $items = ClassStream::query()
            ->with('class')
            ->when($q !== '', fn($query) => $query->where('name','like',"%$q%"))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();
        return view('tenant.academics.class_streams.index', compact('items','q'));
    }

    public function create(Request $request): View
    {
        $classes = SchoolClass::query()->orderBy('name')->pluck('name','id');
        $prefill = (int) $request->query('class_id');
        return view('tenant.academics.class_streams.create', compact('classes','prefill'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'class_id' => ['required','exists:classes,id'],
            'max_capacity' => ['nullable','integer','min:1','max:200'],
        ]);
        ClassStream::create($data);
        return redirect()->route('tenant.academics.class_streams.index')->with('success', __('Stream created.'));
    }

    public function show(ClassStream $class_stream): View
    {
        return view('tenant.academics.class_streams.show', ['item' => $class_stream]);
    }

    public function edit(ClassStream $class_stream): View
    {
        $classes = SchoolClass::query()->orderBy('name')->pluck('name','id');
        return view('tenant.academics.class_streams.edit', ['item' => $class_stream, 'classes' => $classes]);
    }

    public function update(Request $request, ClassStream $class_stream): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'class_id' => ['required','exists:classes,id'],
            'max_capacity' => ['nullable','integer','min:1','max:200'],
        ]);
        $class_stream->update($data);
        return redirect()->route('tenant.academics.class_streams.show', $class_stream)->with('success', __('Stream updated.'));
    }

    public function destroy(ClassStream $class_stream): RedirectResponse
    {
        $class_stream->delete();
        return redirect()->route('tenant.academics.class_streams.index')->with('success', __('Stream deleted.'));
    }

    // Lightweight JSON options endpoint for async stream loading by class
    public function options(Request $request): JsonResponse
    {
        $classId = (int) $request->query('class_id');
        $streams = ClassStream::query()
            ->when($classId, fn($q) => $q->where('class_id', $classId))
            ->orderBy('name')
            ->get(['id','name']);
        return response()->json(['data' => $streams]);
    }
}
