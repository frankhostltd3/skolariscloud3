<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\LandingTestimonial;
use Illuminate\Http\Request;

class LandingTestimonialController extends Controller
{
    public function index()
    {
        $testimonials = LandingTestimonial::orderBy('sort_order')->get();
        return view('landlord.landing-testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('landlord.landing-testimonials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'avatar_url' => 'nullable|string|max:255',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        LandingTestimonial::create([
            'name' => $request->name,
            'role' => $request->role,
            'content' => $request->content,
            'rating' => $request->rating,
            'avatar_url' => $request->avatar_url,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('landlord.landing-testimonials.index')->with('success', 'Testimonial created successfully.');
    }

    public function edit(LandingTestimonial $landingTestimonial)
    {
        return view('landlord.landing-testimonials.edit', compact('landingTestimonial'));
    }

    public function update(Request $request, LandingTestimonial $landingTestimonial)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'avatar_url' => 'nullable|string|max:255',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $landingTestimonial->update([
            'name' => $request->name,
            'role' => $request->role,
            'content' => $request->content,
            'rating' => $request->rating,
            'avatar_url' => $request->avatar_url,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('landlord.landing-testimonials.index')->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(LandingTestimonial $landingTestimonial)
    {
        $landingTestimonial->delete();
        return redirect()->route('landlord.landing-testimonials.index')->with('success', 'Testimonial deleted successfully.');
    }
}
