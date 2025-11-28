<?php

namespace App\Http\Controllers\Tenant\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassRequest;
use App\Http\Requests\UpdateClassRequest;
use App\Models\Academic\ClassRoom;
use App\Models\Academic\EducationLevel;
use App\Models\Academic\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;
        if (!$school) abort(403, 'No school context available.');

        $filters = [
            'q' => trim((string) $request->input('q', '')),
            'education_level_id' => $request->input('education_level_id'),
            'is_active' => $request->input('is_active'),
        ];

        $query = ClassRoom::query()
            ->where('school_id', $school->id)
            ->with(['educationLevel'])
            ->withCount([
                'enrollments as active_enrollments_count' => function ($q) {
                    $q->where('status', 'active');
                },
            ]);

        if ($filters['q'] !== '') {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            });
        }

        if (!empty($filters['education_level_id'])) {
            $query->where('education_level_id', $filters['education_level_id']);
        }

        if ($filters['is_active'] !== null && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active'] === '1');
        }

        $classes = $query->orderBy('name')->paginate(perPage());
        $educationLevels = EducationLevel::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $baseQuery = ClassRoom::query()->where('school_id', $school->id);
        $stats = [
            'total_classes' => (clone $baseQuery)->count(),
            'active_classes' => (clone $baseQuery)->where('is_active', true)->count(),
            'total_students' => Enrollment::whereHas('class', function ($query) use ($school) {
                $query->where('school_id', $school->id);
            })->where('status', 'active')->count(),
            'total_capacity' => (clone $baseQuery)->sum('capacity'),
        ];

        return view('tenant.academics.classes.index', compact('classes', 'educationLevels', 'filters', 'stats'));
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

        $class->load(['educationLevel', 'streams', 'subjects']);
        $class->loadCount([
            'streams as streams_count',
            'subjects as subjects_count',
            'enrollments as active_enrollments_count' => function ($q) {
                $q->where('status', 'active');
            },
        ]);
        $studentsCount = $class->computed_students_count;
        $stats = [
            'streams_count' => $class->streams_count ?? $class->streams()->count(),
            'subjects_count' => $class->subjects_count ?? DB::table('class_subject')->where('class_id', $class->id)->count(),
            'students_count' => $studentsCount,
            'capacity_used' => $class->capacity ? round(($studentsCount / $class->capacity) * 100, 1) : 0,
        ];

        $recentEnrollments = $class->enrollments()
            ->with(['student:id,name,email', 'stream:id,name', 'academicYear:id,name'])
            ->orderByDesc('enrollment_date')
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();

        return view('tenant.academics.classes.show', compact('class', 'stats', 'recentEnrollments'));
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

    public function manageSubjects(Request $request, ClassRoom $class)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;
        if (!$school || $class->school_id !== $school->id) abort(403, 'Unauthorized access.');

        $class->load('subjects');
        $subjects = \App\Models\Academic\Subject::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.academics.classes.manage-subjects', compact('class', 'subjects'));
    }

    public function updateSubjects(Request $request, ClassRoom $class)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;
        if (!$school || $class->school_id !== $school->id) abort(403, 'Unauthorized access.');

        $request->validate([
            'subjects' => 'array',
            'subjects.*' => 'exists:subjects,id',
        ]);

        DB::beginTransaction();
        try {
            $syncData = [];
            if ($request->has('subjects')) {
                foreach ($request->subjects as $subjectId) {
                    $syncData[$subjectId] = ['is_compulsory' => true];
                }
            }

            $class->subjects()->sync($syncData);

            DB::commit();
            return redirect()->route('tenant.academics.classes.show', $class)->with('success', 'Class subjects updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update subjects: ' . $e->getMessage());
        }
    }
}
