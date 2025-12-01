<?php

namespace App\Http\Controllers;

use App\Models\BillingPlan;
use App\Models\HeroSlide;
use App\Models\LandingFaq;
use App\Models\LandingFeature;
use App\Models\LandingPage;
use App\Models\LandingSection;
use App\Models\LandingStat;
use App\Models\LandingTestimonial;
use App\Models\School;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index(Request $request)
    {
        // Fetch active billing plans ordered by position
        $plans = BillingPlan::active()->ordered()->get();

        // Fetch active hero slides ordered by sort_order
        $slides = HeroSlide::where('is_active', true)->orderBy('sort_order')->get();

        // Fetch active landing features ordered by sort_order
        $features = LandingFeature::where('is_active', true)->orderBy('sort_order')->get();

        // Fetch active stats
        $stats = LandingStat::active()->ordered()->get();

        // Fetch active testimonials
        $testimonials = LandingTestimonial::active()->ordered()->get();

        // Fetch active FAQs
        $faqs = LandingFaq::active()->ordered()->get();

        // Fetch active sections
        $sections = LandingSection::active()->ordered()->get();

        // Always show the marketing landing page at root
        // Users can navigate to login/register from there
        return view('home', compact('plans', 'slides', 'features', 'stats', 'testimonials', 'faqs', 'sections'));
    }

    public function page($slug)
    {
        $page = LandingPage::where('slug', $slug)->where('is_active', true)->where('is_published', true)->firstOrFail();
        return view('page', compact('page'));
    }
}
