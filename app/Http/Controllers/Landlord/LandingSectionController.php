<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\LandingSection;
use Illuminate\Http\Request;

class LandingSectionController extends Controller
{
    public function index()
    {
        $sections = LandingSection::orderBy('sort_order')->get();
        return view('landlord.landing-sections.index', compact('sections'));
    }

    public function create()
    {
        return view('landlord.landing-sections.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'component' => 'required|string|max:255',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        LandingSection::create([
            'name' => $request->name,
            'component' => $request->component,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('landlord.landing-sections.index')->with('success', 'Section created successfully.');
    }

    public function edit(LandingSection $landingSection)
    {
        return view('landlord.landing-sections.edit', compact('landingSection'));
    }

    public function update(Request $request, LandingSection $landingSection)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'component' => 'required|string|max:255',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $landingSection->update([
            'name' => $request->name,
            'component' => $request->component,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('landlord.landing-sections.index')->with('success', 'Section updated successfully.');
    }

    public function destroy(LandingSection $landingSection)
    {
        $landingSection->delete();
        return redirect()->route('landlord.landing-sections.index')->with('success', 'Section deleted successfully.');
    }
}
