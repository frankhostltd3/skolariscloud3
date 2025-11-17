<?php

namespace App\Http\Controllers\Tenant\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Academic\Subject;
use App\Models\Academic\EducationLevel;
use App\Models\Academic\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    /**
     * Display a listing of subjects
     */
    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $query = Subject::forSchool($school->id)
            ->with('educationLevel')
            ->withCount('classes');

        // Search functionality
        if ($q = $request->input('q')) {
            $query->where(function($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('code', 'like', "%{$q}%");
            });
        }

        // Filter by type
        if ($type = $request->input('type')) {
            $query->byType($type);
        }

        // Filter by education level
        if ($levelId = $request->input('education_level_id')) {
            $query->byEducationLevel($levelId);
        }

        // Filter by status
        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->active();
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $subjects = $query->orderBy('sort_order')->orderBy('name')->paginate(perPage());

        // Get education levels for filter
        $educationLevels = EducationLevel::forSchool($school->id)->active()->orderBy('sort_order')->get();

        return view('tenant.academics.subjects.index', compact('subjects', 'educationLevels', 'q', 'type', 'levelId'));
    }

    /**
     * Show the form for creating a new subject
     */
    public function create(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;
        $educationLevels = EducationLevel::forSchool($school->id)->active()->orderBy('sort_order')->get();

        return view('tenant.academics.subjects.create', compact('educationLevels'));
    }

    /**
     * Store a newly created subject
     */
    public function store(StoreSubjectRequest $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        try {
            $subject = Subject::create([
                'school_id' => $school->id,
                'name' => $request->name,
                'code' => $request->code,
                'education_level_id' => $request->education_level_id,
                'description' => $request->description,
                'type' => $request->type,
                'credit_hours' => $request->credit_hours,
                'pass_mark' => $request->pass_mark ?? 40,
                'max_marks' => $request->max_marks ?? 100,
                'is_active' => $request->boolean('is_active', true),
                'sort_order' => $request->sort_order ?? 0,
            ]);

            return redirect()->route('tenant.academics.subjects.show', $subject)
                ->with('success', 'Subject created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating subject: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified subject
     */
    public function show(Request $request, Subject $subject)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure subject belongs to current school
        if ($subject->school_id !== $school->id) {
            abort(403);
        }

        $subject->load(['educationLevel', 'classes.streams']);

        return view('tenant.academics.subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified subject
     */
    public function edit(Request $request, Subject $subject)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure subject belongs to current school
        if ($subject->school_id !== $school->id) {
            abort(403);
        }

        $educationLevels = EducationLevel::forSchool($school->id)->active()->orderBy('sort_order')->get();

        return view('tenant.academics.subjects.edit', compact('subject', 'educationLevels'));
    }

    /**
     * Update the specified subject
     */
    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure subject belongs to current school
        if ($subject->school_id !== $school->id) {
            abort(403);
        }

        try {
            $subject->update([
                'name' => $request->name,
                'code' => $request->code,
                'education_level_id' => $request->education_level_id,
                'description' => $request->description,
                'type' => $request->type,
                'credit_hours' => $request->credit_hours,
                'pass_mark' => $request->pass_mark ?? 40,
                'max_marks' => $request->max_marks ?? 100,
                'is_active' => $request->boolean('is_active', true),
                'sort_order' => $request->sort_order ?? 0,
            ]);

            return redirect()->route('tenant.academics.subjects.show', $subject)
                ->with('success', 'Subject updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating subject: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified subject
     */
    public function destroy(Request $request, Subject $subject)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure subject belongs to current school
        if ($subject->school_id !== $school->id) {
            abort(403);
        }

        try {
            // Check if subject is assigned to classes
            if ($subject->classes()->exists()) {
                return back()->with('error', 'Cannot delete subject that is assigned to classes. Remove class assignments first.');
            }

            $subject->delete();

            return redirect()->route('tenant.academics.subjects.index')
                ->with('success', 'Subject deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting subject: ' . $e->getMessage());
        }
    }

    /**
     * Assign subject to classes
     */
    public function assignClasses(Request $request, Subject $subject)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure subject belongs to current school
        if ($subject->school_id !== $school->id) {
            abort(403);
        }

        $classes = ClassRoom::forSchool($school->id)->with('educationLevel')->orderBy('name')->get();

        return view('tenant.academics.subjects.assign-classes', compact('subject', 'classes'));
    }

    /**
     * Store class assignments
     */
    public function storeClassAssignments(Request $request, Subject $subject)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure subject belongs to current school
        if ($subject->school_id !== $school->id) {
            abort(403);
        }

        $request->validate([
            'classes' => 'nullable|array',
            'classes.*' => 'exists:classes,id',
        ]);

        try {
            DB::beginTransaction();

            // Sync classes (this will remove old assignments not in the array)
            $syncData = [];
            if ($request->has('classes')) {
                foreach ($request->classes as $classId) {
                    $syncData[$classId] = ['is_compulsory' => true];
                }
            }

            $subject->classes()->sync($syncData);

            DB::commit();

            return redirect()->route('tenant.academics.subjects.show', $subject)
                ->with('success', 'Class assignments updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating class assignments: ' . $e->getMessage());
        }
    }
}
