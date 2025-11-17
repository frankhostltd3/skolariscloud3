<?php

namespace App\Http\Controllers\Tenant\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassRequest;
use App\Http\Requests\UpdateClassRequest;
use App\Models\Academic\ClassRoom;
use App\Models\Academic\EducationLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;
        if (!$school) abort(403, 'No school context available.');

        $query = ClassRoom::query()->where('school_id', $school->id)->with(['educationLevel', 'streams']);
        if ($request->filled('q')) $query->where('name', 'like', '%' . $request->input('q') . '%');
        if ($request->filled('education_level_id')) $query->where('education_level_id', $request->input('education_level_id'));
        if ($request->filled('status') && $request->status !== 'all') $query->where('is_active', $request->status === 'active');

        $classes = $query->orderBy('name')->paginate(perPage());
        $educationLevels = EducationLevel::where('school_id', $school->id)->where('is_active', true)->orderBy('sort_order')->get();

        return view('tenant.academics.classes.index', compact('classes', 'educationLevels'));
    }

    public function create(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;
        if (!$school) abort(403, 'No school context available.');

        $educationLevels = EducationLevel::where('school_id', $school->id)->where('is_active', true)->orderBy('sort_order')->get();
        return view('tenant.academics.classes.create', compact('educationLevels'));
    }

    public function store(StoreClassRequest $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;
        if (!$school) abort(403, 'No school context available.');

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['school_id'] = $school->id;
            ClassRoom::create($data);
            DB::commit();
            return redirect()->route('tenant.academics.classes.index')->with('success', 'Class created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to create class: ' . $e->getMessage());
        }
    }

    public function show(Request $request, ClassRoom $class)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;
        if (!$school || $class->school_id !== $school->id) abort(403, 'Unauthorized access.');

        $class->load(['educationLevel', 'streams']);
        $stats = [
            'streams_count' => $class->streams()->count(),
            'subjects_count' => DB::table('class_subject')->where('class_id', $class->id)->count(),
            'students_count' => 0,
            'capacity_used' => $class->capacity > 0 ? round(($class->students_count / $class->capacity) * 100, 1) : 0,
        ];
        return view('tenant.academics.classes.show', compact('class', 'stats'));
    }

    public function edit(Request $request, ClassRoom $class)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;
        if (!$school || $class->school_id !== $school->id) abort(403, 'Unauthorized access.');

        $educationLevels = EducationLevel::where('school_id', $school->id)->where('is_active', true)->orderBy('sort_order')->get();
        return view('tenant.academics.classes.edit', compact('class', 'educationLevels'));
    }

    public function update(UpdateClassRequest $request, ClassRoom $class)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;
        if (!$school || $class->school_id !== $school->id) abort(403, 'Unauthorized access.');

        DB::beginTransaction();
        try {
            $class->update($request->validated());
            DB::commit();
            return redirect()->route('tenant.academics.classes.index')->with('success', 'Class updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to update class: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, ClassRoom $class)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;
        if (!$school || $class->school_id !== $school->id) abort(403, 'Unauthorized access.');

        if ($class->streams()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete class with existing streams. Please delete streams first.');
        }

        $subjectsCount = DB::table('class_subject')->where('class_id', $class->id)->count();
        if ($subjectsCount > 0) {
            return redirect()->back()->with('error', 'Cannot delete class with assigned subjects. Please remove subjects first.');
        }

        DB::beginTransaction();
        try {
            $class->delete();
            DB::commit();
            return redirect()->route('tenant.academics.classes.index')->with('success', 'Class deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete class: ' . $e->getMessage());
        }
    }
}
