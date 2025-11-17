<?php

namespace App\Http\Controllers\Tenant\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGradingSchemeRequest;
use App\Http\Requests\UpdateGradingSchemeRequest;
use App\Models\Academic\GradingScheme;
use App\Models\Academic\GradingBand;
use App\Models\Academic\ExaminationBody;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradingSchemeController extends Controller
{
    /**
     * Display a listing of grading schemes
     */
    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $query = GradingScheme::forSchool($school->id)
            ->withCount('bands')
            ->with('examinationBody');

        // Search functionality
        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%{$q}%")
                  ->orWhere('country', 'like', "%{$q}%");
        }

        $items = $query->latest()->paginate(perPage());

        return view('tenant.academics.grading_schemes.index', compact('items', 'q'));
    }

    /**
     * Show the form for creating a new grading scheme
     */
    public function create(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;
        $examinationBodies = ExaminationBody::forSchool($school->id)->active()->get();

        return view('tenant.academics.grading_schemes.create', compact('examinationBodies'));
    }

    /**
     * Store a newly created grading scheme
     */
    public function store(StoreGradingSchemeRequest $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        try {
            DB::beginTransaction();

            $gradingScheme = GradingScheme::create([
                'school_id' => $school->id,
                'name' => $request->name,
                'country' => $request->country,
                'examination_body_id' => $request->examination_body_id,
                'description' => $request->description,
                'is_current' => $request->boolean('is_current'),
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Create grading bands if provided
            if ($request->has('bands')) {
                foreach ($request->bands as $index => $bandData) {
                    if (empty($bandData['grade']) || empty($bandData['min_score']) || empty($bandData['max_score'])) {
                        continue;
                    }

                    GradingBand::create([
                        'grading_scheme_id' => $gradingScheme->id,
                        'grade' => $bandData['grade'],
                        'label' => $bandData['label'] ?? null,
                        'min_score' => $bandData['min_score'],
                        'max_score' => $bandData['max_score'],
                        'grade_point' => $bandData['grade_point'] ?? null,
                        'remarks' => $bandData['remarks'] ?? null,
                        'sort_order' => $index,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('tenant.academics.grading_schemes.show', $gradingScheme)
                ->with('success', 'Grading scheme created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating grading scheme: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified grading scheme
     */
    public function show(Request $request, GradingScheme $gradingScheme)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure grading scheme belongs to current school
        if ($gradingScheme->school_id !== $school->id) {
            abort(403);
        }

        $gradingScheme->load(['bands', 'examinationBody']);

        return view('tenant.academics.grading_schemes.show', compact('gradingScheme'));
    }

    /**
     * Show the form for editing the specified grading scheme
     */
    public function edit(Request $request, GradingScheme $gradingScheme)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure grading scheme belongs to current school
        if ($gradingScheme->school_id !== $school->id) {
            abort(403);
        }

        $gradingScheme->load('bands');
        $examinationBodies = ExaminationBody::forSchool($school->id)->active()->get();

        return view('tenant.academics.grading_schemes.edit', compact('gradingScheme', 'examinationBodies'));
    }

    /**
     * Update the specified grading scheme
     */
    public function update(UpdateGradingSchemeRequest $request, GradingScheme $gradingScheme)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure grading scheme belongs to current school
        if ($gradingScheme->school_id !== $school->id) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $gradingScheme->update([
                'name' => $request->name,
                'country' => $request->country,
                'examination_body_id' => $request->examination_body_id,
                'description' => $request->description,
                'is_current' => $request->boolean('is_current'),
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Delete existing bands
            $gradingScheme->bands()->delete();

            // Create new bands if provided
            if ($request->has('bands')) {
                foreach ($request->bands as $index => $bandData) {
                    if (empty($bandData['grade']) || empty($bandData['min_score']) || empty($bandData['max_score'])) {
                        continue;
                    }

                    GradingBand::create([
                        'grading_scheme_id' => $gradingScheme->id,
                        'grade' => $bandData['grade'],
                        'label' => $bandData['label'] ?? null,
                        'min_score' => $bandData['min_score'],
                        'max_score' => $bandData['max_score'],
                        'grade_point' => $bandData['grade_point'] ?? null,
                        'remarks' => $bandData['remarks'] ?? null,
                        'sort_order' => $index,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('tenant.academics.grading_schemes.show', $gradingScheme)
                ->with('success', 'Grading scheme updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating grading scheme: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified grading scheme
     */
    public function destroy(Request $request, GradingScheme $gradingScheme)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure grading scheme belongs to current school
        if ($gradingScheme->school_id !== $school->id) {
            abort(403);
        }

        try {
            $gradingScheme->delete();
            return redirect()->route('tenant.academics.grading_schemes.index')
                ->with('success', 'Grading scheme deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting grading scheme: ' . $e->getMessage());
        }
    }

    /**
     * Set grading scheme as current
     */
    public function setCurrent(Request $request, GradingScheme $gradingScheme)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure grading scheme belongs to current school
        if ($gradingScheme->school_id !== $school->id) {
            abort(403);
        }

        try {
            $gradingScheme->update(['is_current' => true]);
            return back()->with('success', 'Grading scheme set as current successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error setting current grading scheme: ' . $e->getMessage());
        }
    }

    /**
     * Export all grading schemes (placeholder)
     */
    public function exportAll(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        // TODO: Implement export functionality (CSV, PDF, Excel)
        return back()->with('info', 'Export functionality coming soon.');
    }
}
