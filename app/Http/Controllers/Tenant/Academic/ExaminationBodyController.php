<?php

namespace App\Http\Controllers\Tenant\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExaminationBodyRequest;
use App\Http\Requests\UpdateExaminationBodyRequest;
use App\Models\Academic\Country;
use App\Models\Academic\ExaminationBody;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExaminationBodyController extends Controller
{
    /**
     * Display a listing of examination bodies.
     */
    public function index()
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        $examinationBodies = ExaminationBody::forSchool($school->id)
            ->with('country')
            ->orderBy('name')
            ->paginate(15);

        return view('tenant.academics.examination-bodies.index', compact('examinationBodies'));
    }

    /**
     * Show the form for creating a new examination body.
     */
    public function create()
    {
        $countries = Country::active()->orderBy('name')->get();

        return view('tenant.academics.examination-bodies.create', compact('countries'));
    }

    /**
     * Store a newly created examination body in storage.
     */
    public function store(StoreExaminationBodyRequest $request)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        try {
            DB::beginTransaction();

            ExaminationBody::create([
                'school_id' => $school->id,
                'name' => $request->name,
                'code' => $request->code,
                'country_id' => $request->country_id,
                'website' => $request->website,
                'description' => $request->description,
                'is_international' => $request->is_international ?? false,
                'is_active' => $request->is_active ?? true,
            ]);

            DB::commit();

            return redirect()
                ->route('tenant.academics.examination-bodies.index')
                ->with('success', __('Examination body created successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', __('Failed to create examination body. Please try again.'));
        }
    }

    /**
     * Display the specified examination body.
     */
    public function show(ExaminationBody $examinationBody)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure examination body belongs to current school
        if ($examinationBody->school_id !== $school->id) {
            abort(403, 'Unauthorized access to examination body.');
        }

        $examinationBody->load('country');

        return view('tenant.academics.examination-bodies.show', compact('examinationBody'));
    }

    /**
     * Show the form for editing the specified examination body.
     */
    public function edit(ExaminationBody $examinationBody)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure examination body belongs to current school
        if ($examinationBody->school_id !== $school->id) {
            abort(403, 'Unauthorized access to examination body.');
        }

        $countries = Country::active()->orderBy('name')->get();

        return view('tenant.academics.examination-bodies.edit', compact('examinationBody', 'countries'));
    }

    /**
     * Update the specified examination body in storage.
     */
    public function update(UpdateExaminationBodyRequest $request, ExaminationBody $examinationBody)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure examination body belongs to current school
        if ($examinationBody->school_id !== $school->id) {
            abort(403, 'Unauthorized access to examination body.');
        }

        try {
            DB::beginTransaction();

            $examinationBody->update([
                'name' => $request->name,
                'code' => $request->code,
                'country_id' => $request->country_id,
                'website' => $request->website,
                'description' => $request->description,
                'is_international' => $request->is_international ?? $examinationBody->is_international,
                'is_active' => $request->is_active ?? $examinationBody->is_active,
            ]);

            DB::commit();

            return redirect()
                ->route('tenant.academics.examination-bodies.index')
                ->with('success', __('Examination body updated successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', __('Failed to update examination body. Please try again.'));
        }
    }

    /**
     * Remove the specified examination body from storage.
     */
    public function destroy(ExaminationBody $examinationBody)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure examination body belongs to current school
        if ($examinationBody->school_id !== $school->id) {
            abort(403, 'Unauthorized access to examination body.');
        }

        try {
            $examinationBody->delete();

            return redirect()
                ->route('tenant.academics.examination-bodies.index')
                ->with('success', __('Examination body deleted successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Failed to delete examination body. Please try again.'));
        }
    }
}
