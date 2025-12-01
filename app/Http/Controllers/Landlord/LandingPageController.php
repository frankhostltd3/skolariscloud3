<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandingPageController extends Controller
{
    public function index()
    {
        $pages = LandingPage::all();
        return view('landlord.landing-pages.index', compact('pages'));
    }

    public function create()
    {
        return view('landlord.landing-pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'slug' => 'nullable|string|max:255|unique:landing_pages,slug',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_published' => 'boolean',
        ]);

        LandingPage::create([
            'title' => $request->title,
            'slug' => $request->slug ?? Str::slug($request->title),
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'is_active' => $request->has('is_active'),
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('landlord.landing-pages.index')->with('success', 'Page created successfully.');
    }

    public function edit(LandingPage $landingPage)
    {
        return view('landlord.landing-pages.edit', compact('landingPage'));
    }

    public function update(Request $request, LandingPage $landingPage)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'slug' => 'nullable|string|max:255|unique:landing_pages,slug,' . $landingPage->id,
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_published' => 'boolean',
        ]);

        $landingPage->update([
            'title' => $request->title,
            'slug' => $request->slug ?? Str::slug($request->title),
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'is_active' => $request->has('is_active'),
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('landlord.landing-pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(LandingPage $landingPage)
    {
        $landingPage->delete();
        return redirect()->route('landlord.landing-pages.index')->with('success', 'Page deleted successfully.');
    }
}
