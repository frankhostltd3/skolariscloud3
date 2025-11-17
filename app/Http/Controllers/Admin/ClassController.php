<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Academic\EducationLevel;
use App\Models\Academic\ClassStream;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $query = SchoolClass::forSchool($school->id)->with(['educationLevel', 'stream']);

        if ($request->filled('level_id')) {
            $query->where('education_level_id', $request->input('level_id'));
        }

        if ($request->filled('stream_id')) {
            $query->where('class_stream_id', $request->input('stream_id'));
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $classes = $query->orderBy('name')->paginate(perPage());

        return view('admin.classes.index', [
            'classes'        => $classes,
            'levels'         => EducationLevel::forSchool($school->id)->orderBy('sort_order')->get(),
            'streams'        => ClassStream::forSchool($school->id)->orderBy('name')->get(),
            'filters'        => $request->only(['level_id', 'stream_id', 'status']),
        ]);
    }

    public function create(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        return view('admin.classes.create', [
            'levels'  => EducationLevel::forSchool($school->id)->orderBy('sort_order')->get(),
            'streams' => ClassStream::forSchool($school->id)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $data = $request->validate([
            'name'               => ['required', 'string', 'max:100'],
            'education_level_id' => ['required', 'exists:tenant.education_levels,id'],
            'class_stream_id'    => ['nullable', 'exists:tenant.class_streams,id'],
            'capacity'           => ['nullable', 'integer', 'min:1', 'max:200'],
            'room_number'        => ['nullable', 'string', 'max:50'],
            'is_active'          => ['required', 'boolean'],
        ]);

        $data['school_id'] = $school->id;

        SchoolClass::create($data);

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function edit(Request $request, SchoolClass $class)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        abort_unless($class->school_id === $school->id, 404);

        return view('admin.classes.edit', [
            'class'   => $class,
            'levels'  => EducationLevel::forSchool($school->id)->orderBy('sort_order')->get(),
            'streams' => ClassStream::forSchool($school->id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, SchoolClass $class)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;
        abort_unless($class->school_id === $school->id, 404);

        $data = $request->validate([
            'name'               => ['required', 'string', 'max:100'],
            'education_level_id' => ['required', 'exists:tenant.education_levels,id'],
            'class_stream_id'    => ['nullable', 'exists:tenant.class_streams,id'],
            'capacity'           => ['nullable', 'integer', 'min:1', 'max:200'],
            'room_number'        => ['nullable', 'string', 'max:50'],
            'is_active'          => ['required', 'boolean'],
        ]);

        $class->update($data);

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(Request $request, SchoolClass $class)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;
        abort_unless($class->school_id === $school->id, 404);

        $class->delete();

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Class deleted successfully.');
    }
}
