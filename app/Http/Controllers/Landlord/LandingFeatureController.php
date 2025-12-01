<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\LandingFeature;
use Illuminate\Http\Request;

class LandingFeatureController extends Controller
{
    public function index()
    {
        $features = LandingFeature::orderBy('sort_order')->get();
        return view('landlord.landing-features.index', compact('features'));
    }

    public function create()
    {
        return view('landlord.landing-features.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string|max:50',
            'icon_color' => 'nullable|string|max:50',
            'icon_bg_color' => 'nullable|string|max:50',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        LandingFeature::create([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
            'icon_color' => $request->icon_color,
            'icon_bg_color' => $request->icon_bg_color,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('landlord.landing-features.index')->with('success', 'Feature created successfully.');
    }

    public function edit(LandingFeature $landingFeature)
    {
        return view('landlord.landing-features.edit', compact('landingFeature'));
    }

    public function update(Request $request, LandingFeature $landingFeature)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string|max:50',
            'icon_color' => 'nullable|string|max:50',
            'icon_bg_color' => 'nullable|string|max:50',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $landingFeature->update([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
            'icon_color' => $request->icon_color,
            'icon_bg_color' => $request->icon_bg_color,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('landlord.landing-features.index')->with('success', 'Feature updated successfully.');
    }

    public function destroy(LandingFeature $landingFeature)
    {
        $landingFeature->delete();
        return redirect()->route('landlord.landing-features.index')->with('success', 'Feature deleted successfully.');
    }
}
