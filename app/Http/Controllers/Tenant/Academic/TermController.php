<?php

namespace App\Http\Controllers\Tenant\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTermRequest;
use App\Http\Requests\UpdateTermRequest;
use App\Models\Academic\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TermController extends Controller
{
    /**
     * Display a listing of terms
     */
    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        if (!$school) {
            abort(403, 'No school context available.');
        }

        $query = Term::where('school_id', $school->id);

        // Search functionality
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('q') . '%')
                  ->orWhere('academic_year', 'like', '%' . $request->input('q') . '%')
                  ->orWhere('code', 'like', '%' . $request->input('q') . '%');
            });
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->input('academic_year'));
        }

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->input('status')) {
                case 'current':
                    $query->where('is_current', true);
                    break;
                case 'ongoing':
                    $query->ongoing();
                    break;
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'past':
                    $query->past();
                    break;
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        $terms = $query->orderBy('start_date', 'desc')->paginate(perPage());

        // Get distinct academic years for filter
        $academicYears = Term::where('school_id', $school->id)
            ->distinct()
            ->pluck('academic_year')
            ->sort()
            ->values();

        return view('tenant.academics.terms.index', compact('terms', 'academicYears'));
    }

    /**
     * Show the form for creating a new term
     */
    public function create(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        if (!$school) {
            abort(403, 'No school context available.');
        }

        return view('tenant.academics.terms.create');
    }

    /**
     * Store a newly created term in storage
     */
    public function store(StoreTermRequest $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        if (!$school) {
            abort(403, 'No school context available.');
        }

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['school_id'] = $school->id;

            // If this is set as current, unset others
            if ($data['is_current'] ?? false) {
                Term::where('school_id', $school->id)
                    ->update(['is_current' => false]);
            }

            $term = Term::create($data);

            DB::commit();

            return redirect()
                ->route('tenant.academics.terms.index')
                ->with('success', 'Term created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create term: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified term
     */
    public function show(Request $request, Term $term)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        if (!$school || $term->school_id !== $school->id) {
            abort(403, 'Unauthorized access.');
        }

        // Get statistics (placeholders for future integration)
        $stats = [
            'classes_count' => 0, // Future: classes taught in this term
            'subjects_count' => 0, // Future: subjects taught in this term
            'students_count' => 0, // Future: students enrolled in this term
            'exams_count' => 0, // Future: exams scheduled in this term
        ];

        return view('tenant.academics.terms.show', compact('term', 'stats'));
    }

    /**
     * Show the form for editing the specified term
     */
    public function edit(Request $request, Term $term)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        if (!$school || $term->school_id !== $school->id) {
            abort(403, 'Unauthorized access.');
        }

        return view('tenant.academics.terms.edit', compact('term'));
    }

    /**
     * Update the specified term in storage
     */
    public function update(UpdateTermRequest $request, Term $term)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        if (!$school || $term->school_id !== $school->id) {
            abort(403, 'Unauthorized access.');
        }

        DB::beginTransaction();
        try {
            $data = $request->validated();

            // If this is set as current, unset others
            if (($data['is_current'] ?? false) && !$term->is_current) {
                Term::where('school_id', $school->id)
                    ->where('id', '!=', $term->id)
                    ->update(['is_current' => false]);
            }

            $term->update($data);

            DB::commit();

            return redirect()
                ->route('tenant.academics.terms.index')
                ->with('success', 'Term updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update term: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified term from storage
     */
    public function destroy(Request $request, Term $term)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        if (!$school || $term->school_id !== $school->id) {
            abort(403, 'Unauthorized access.');
        }

        // Prevent deletion of current term
        if ($term->is_current) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete the current term. Please set another term as current first.');
        }

        DB::beginTransaction();
        try {
            $term->delete();

            DB::commit();

            return redirect()
                ->route('tenant.academics.terms.index')
                ->with('success', 'Term deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to delete term: ' . $e->getMessage());
        }
    }

    /**
     * Set a term as the current term
     */
    public function setCurrent(Request $request, Term $term)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        if (!$school || $term->school_id !== $school->id) {
            abort(403, 'Unauthorized access.');
        }

        DB::beginTransaction();
        try {
            $term->setAsCurrent();

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Term set as current successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to set term as current: ' . $e->getMessage());
        }
    }
}
