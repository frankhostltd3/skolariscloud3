<?php

namespace App\Http\Controllers\Tenant\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Models\Academic\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    /**
     * Display a listing of countries.
     */
    public function index()
    {
        $countries = Country::withCount('examinationBodies')
            ->orderBy('name')
            ->paginate(20);

        return view('tenant.academics.countries.index', compact('countries'));
    }

    /**
     * Show the form for creating a new country.
     */
    public function create()
    {
        return view('tenant.academics.countries.create');
    }

    /**
     * Store a newly created country in storage.
     */
    public function store(StoreCountryRequest $request)
    {
        try {
            DB::beginTransaction();

            Country::create([
                'name' => $request->name,
                'iso_code_2' => strtoupper($request->iso_code_2),
                'iso_code_3' => strtoupper($request->iso_code_3),
                'phone_code' => $request->phone_code,
                'currency_code' => strtoupper($request->currency_code),
                'currency_symbol' => $request->currency_symbol,
                'timezone' => $request->timezone,
                'flag_emoji' => $request->flag_emoji,
                'is_active' => $request->is_active ?? true,
            ]);

            DB::commit();

            return redirect()
                ->route('tenant.academics.countries.index')
                ->with('success', __('Country created successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', __('Failed to create country. Please try again.'));
        }
    }

    /**
     * Display the specified country.
     */
    public function show(Country $country)
    {
        $country->load('examinationBodies');

        return view('tenant.academics.countries.show', compact('country'));
    }

    /**
     * Show the form for editing the specified country.
     */
    public function edit(Country $country)
    {
        return view('tenant.academics.countries.edit', compact('country'));
    }

    /**
     * Update the specified country in storage.
     */
    public function update(UpdateCountryRequest $request, Country $country)
    {
        try {
            DB::beginTransaction();

            $country->update([
                'name' => $request->name,
                'iso_code_2' => strtoupper($request->iso_code_2),
                'iso_code_3' => strtoupper($request->iso_code_3),
                'phone_code' => $request->phone_code,
                'currency_code' => strtoupper($request->currency_code),
                'currency_symbol' => $request->currency_symbol,
                'timezone' => $request->timezone,
                'flag_emoji' => $request->flag_emoji,
                'is_active' => $request->is_active ?? $country->is_active,
            ]);

            DB::commit();

            return redirect()
                ->route('tenant.academics.countries.index')
                ->with('success', __('Country updated successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', __('Failed to update country. Please try again.'));
        }
    }

    /**
     * Remove the specified country from storage.
     */
    public function destroy(Country $country)
    {
        // Check if country has examination bodies
        if ($country->examinationBodies()->count() > 0) {
            return back()->with('error', __('Cannot delete country with associated examination bodies.'));
        }

        try {
            $country->delete();

            return redirect()
                ->route('tenant.academics.countries.index')
                ->with('success', __('Country deleted successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Failed to delete country. Please try again.'));
        }
    }
}
