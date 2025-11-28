<?php

namespace App\Http\Controllers\Tenant\Finance;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        $query = FeeStructure::where('school_id', $school->id);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fee_name', 'like', "%{$search}%")
                  ->orWhere('fee_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by fee type
        if ($request->filled('fee_type')) {
            $query->where('fee_type', $request->fee_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $feeStructures = $query->orderBy('academic_year', 'desc')
            ->orderBy('fee_name')
            ->paginate(perPage());

        // Get distinct academic years
        $academicYears = FeeStructure::where('school_id', $school->id)
            ->select('academic_year')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');

        // Get distinct fee types
        $feeTypes = FeeStructure::where('school_id', $school->id)
            ->select('fee_type')
            ->distinct()
            ->orderBy('fee_type')
            ->pluck('fee_type');

        return view('tenant.finance.fee-structures.index', compact('feeStructures', 'academicYears', 'feeTypes'));
    }

    public function create()
    {
        $school = request()->attributes->get('currentSchool');

        // Get classes for dropdown
        $classes = \App\Models\Academic\ClassRoom::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.finance.fee-structures.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        $validated = $request->validate([
            'fee_name' => 'required|string|max:191',
            'fee_type' => 'required|string|max:191',
            'amount' => 'required|numeric|min:0',
            'academic_year' => 'required|string|max:191',
            'term' => 'nullable|string|max:191',
            'class' => 'nullable|string|max:191',
            'due_date' => 'nullable|date',
            'is_mandatory' => 'boolean',
            'is_recurring' => 'boolean',
            'frequency' => 'nullable|string|in:once,per_term,per_year',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $validated['school_id'] = $school->id;
        $validated['is_mandatory'] = $request->has('is_mandatory');
        $validated['is_recurring'] = $request->has('is_recurring');
        $validated['is_active'] = $request->has('is_active');

        FeeStructure::create($validated);

        return redirect()
            ->route('tenant.finance.fee-structures.index')
            ->with('success', 'Fee structure created successfully.');
    }

    public function show(FeeStructure $feeStructure)
    {
        $feeStructure->load('invoices');

        $stats = [
            'total_invoices' => $feeStructure->invoices()->count(),
            'total_amount' => $feeStructure->invoices()->sum('total_amount'),
            'paid_amount' => $feeStructure->invoices()->sum('paid_amount'),
            'outstanding' => $feeStructure->invoices()->sum('balance'),
        ];

        return view('tenant.finance.fee-structures.show', compact('feeStructure', 'stats'));
    }

    public function edit(FeeStructure $feeStructure)
    {
        $school = request()->attributes->get('currentSchool');

        // Get classes for dropdown
        $classes = \App\Models\Academic\ClassRoom::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.finance.fee-structures.edit', compact('feeStructure', 'classes'));
    }

    public function update(Request $request, FeeStructure $feeStructure)
    {
        $validated = $request->validate([
            'fee_name' => 'required|string|max:191',
            'fee_type' => 'required|string|max:191',
            'amount' => 'required|numeric|min:0',
            'academic_year' => 'required|string|max:191',
            'term' => 'nullable|string|max:191',
            'class' => 'nullable|string|max:191',
            'due_date' => 'nullable|date',
            'is_mandatory' => 'boolean',
            'is_recurring' => 'boolean',
            'frequency' => 'nullable|string|in:once,per_term,per_year',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $validated['is_mandatory'] = $request->has('is_mandatory');
        $validated['is_recurring'] = $request->has('is_recurring');
        $validated['is_active'] = $request->has('is_active');

        $feeStructure->update($validated);

        return redirect()
            ->route('tenant.finance.fee-structures.index')
            ->with('success', 'Fee structure updated successfully.');
    }

    public function destroy(FeeStructure $feeStructure)
    {
        // Check if fee structure has invoices
        if ($feeStructure->invoices()->count() > 0) {
            return redirect()
                ->route('tenant.finance.fee-structures.index')
                ->with('error', 'Cannot delete fee structure with existing invoices.');
        }

        $feeStructure->delete();

        return redirect()
            ->route('tenant.finance.fee-structures.index')
            ->with('success', 'Fee structure deleted successfully.');
    }
}
