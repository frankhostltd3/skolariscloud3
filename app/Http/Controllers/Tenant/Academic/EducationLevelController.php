<?php

namespace App\Http\Controllers\Tenant\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEducationLevelRequest;
use App\Http\Requests\UpdateEducationLevelRequest;
use App\Models\Academic\EducationLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EducationLevelController extends Controller
{
    /**
     * Display a listing of education levels.
     */
    public function index()
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        $educationLevels = EducationLevel::forSchool($school->id)
            ->withCount('classes')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('tenant.academics.education-levels.index', compact('educationLevels'));
    }

    /**
     * Show the form for creating a new education level.
     */
    public function create()
    {
        return view('tenant.academics.education-levels.create');
    }

    /**
     * Store a newly created education level in storage.
     */
    public function store(StoreEducationLevelRequest $request)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        try {
            DB::beginTransaction();

            EducationLevel::create([
                'school_id' => $school->id,
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'min_grade' => $request->min_grade,
                'max_grade' => $request->max_grade,
                'is_active' => $request->is_active ?? true,
                'sort_order' => $request->sort_order ?? 0,
            ]);

            DB::commit();

            return redirect()
                ->route('tenant.academics.education-levels.index')
                ->with('success', __('Education level created successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', __('Failed to create education level. Please try again.'));
        }
    }

    /**
     * Display the specified education level.
     */
    public function show(EducationLevel $educationLevel)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure education level belongs to current school
        if ($educationLevel->school_id !== $school->id) {
            abort(403, 'Unauthorized access to education level.');
        }

        $educationLevel->load(['classes', 'subjects']);

        return view('tenant.academics.education-levels.show', compact('educationLevel'));
    }

    /**
     * Show the form for editing the specified education level.
     */
    public function edit(EducationLevel $educationLevel)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure education level belongs to current school
        if ($educationLevel->school_id !== $school->id) {
            abort(403, 'Unauthorized access to education level.');
        }

        return view('tenant.academics.education-levels.edit', compact('educationLevel'));
    }

    /**
     * Update the specified education level in storage.
     */
    public function update(UpdateEducationLevelRequest $request, EducationLevel $educationLevel)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure education level belongs to current school
        if ($educationLevel->school_id !== $school->id) {
            abort(403, 'Unauthorized access to education level.');
        }

        try {
            DB::beginTransaction();

            $educationLevel->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'min_grade' => $request->min_grade,
                'max_grade' => $request->max_grade,
                'is_active' => $request->is_active ?? $educationLevel->is_active,
                'sort_order' => $request->sort_order ?? $educationLevel->sort_order,
            ]);

            DB::commit();

            return redirect()
                ->route('tenant.academics.education-levels.index')
                ->with('success', __('Education level updated successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', __('Failed to update education level. Please try again.'));
        }
    }

    /**
     * Remove the specified education level from storage.
     */
    public function destroy(EducationLevel $educationLevel)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure education level belongs to current school
        if ($educationLevel->school_id !== $school->id) {
            abort(403, 'Unauthorized access to education level.');
        }

        // Check if education level has classes
        if ($educationLevel->classes()->count() > 0) {
            return back()->with('error', __('Cannot delete education level with assigned classes. Please reassign classes first.'));
        }

        try {
            $educationLevel->delete();

            return redirect()
                ->route('tenant.academics.education-levels.index')
                ->with('success', __('Education level deleted successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Failed to delete education level. Please try again.'));
        }
    }
}
