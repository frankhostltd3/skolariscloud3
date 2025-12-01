<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\LandingStat;
use Illuminate\Http\Request;

class LandingStatController extends Controller
{
    public function index()
    {
        $stats = LandingStat::orderBy('sort_order')->get();
        return view('landlord.landing-stats.index', compact('stats'));
    }

    public function create()
    {
        return view('landlord.landing-stats.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        LandingStat::create([
            'value' => $request->value,
            'label' => $request->label,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('landlord.landing-stats.index')->with('success', 'Stat created successfully.');
    }

    public function edit(LandingStat $landingStat)
    {
        return view('landlord.landing-stats.edit', compact('landingStat'));
    }

    public function update(Request $request, LandingStat $landingStat)
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $landingStat->update([
            'value' => $request->value,
            'label' => $request->label,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('landlord.landing-stats.index')->with('success', 'Stat updated successfully.');
    }

    public function destroy(LandingStat $landingStat)
    {
        $landingStat->delete();
        return redirect()->route('landlord.landing-stats.index')->with('success', 'Stat deleted successfully.');
    }
}
