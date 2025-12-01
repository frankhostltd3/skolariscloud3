<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\LandingFaq;
use Illuminate\Http\Request;

class LandingFaqController extends Controller
{
    public function index()
    {
        $faqs = LandingFaq::orderBy('sort_order')->get();
        return view('landlord.landing-faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('landlord.landing-faqs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        LandingFaq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('landlord.landing-faqs.index')->with('success', 'FAQ created successfully.');
    }

    public function edit(LandingFaq $landingFaq)
    {
        return view('landlord.landing-faqs.edit', compact('landingFaq'));
    }

    public function update(Request $request, LandingFaq $landingFaq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $landingFaq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('landlord.landing-faqs.index')->with('success', 'FAQ updated successfully.');
    }

    public function destroy(LandingFaq $landingFaq)
    {
        $landingFaq->delete();
        return redirect()->route('landlord.landing-faqs.index')->with('success', 'FAQ deleted successfully.');
    }
}
