<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::orderBy('sort_order')->get();
        return view('landlord.hero-slides.index', compact('slides'));
    }

    public function create()
    {
        return view('landlord.hero-slides.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string|max:50',
            'cta_link' => 'nullable|string|max:255',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $path = $request->file('image')->store('hero-slides', 'public');

        HeroSlide::create([
            'image_path' => $path,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'cta_text' => $request->cta_text,
            'cta_link' => $request->cta_link,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('landlord.hero-slides.index')->with('success', 'Slide created successfully.');
    }

    public function edit(HeroSlide $heroSlide)
    {
        return view('landlord.hero-slides.edit', compact('heroSlide'));
    }

    public function update(Request $request, HeroSlide $heroSlide)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string|max:50',
            'cta_link' => 'nullable|string|max:255',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $data = [
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'cta_text' => $request->cta_text,
            'cta_link' => $request->cta_link,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($heroSlide->image_path) {
                Storage::disk('public')->delete($heroSlide->image_path);
            }
            $data['image_path'] = $request->file('image')->store('hero-slides', 'public');
        }

        $heroSlide->update($data);

        return redirect()->route('landlord.hero-slides.index')->with('success', 'Slide updated successfully.');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        if ($heroSlide->image_path) {
            Storage::disk('public')->delete($heroSlide->image_path);
        }
        $heroSlide->delete();

        return redirect()->route('landlord.hero-slides.index')->with('success', 'Slide deleted successfully.');
    }
}
