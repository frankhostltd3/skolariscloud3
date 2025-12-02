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
     * Display the central/landlord landing page.
     * This is the main entry point for the application at the root URL.
     */
    public function index(Request $request)
    {
        // Try to load dynamic content with fallbacks for empty/missing tables
        try {
            $plans = BillingPlan::active()->ordered()->get();
        } catch (\Exception $e) {
            $plans = collect();
        }

        try {
            $slides = HeroSlide::where('is_active', true)->orderBy('sort_order')->get();
        } catch (\Exception $e) {
            $slides = collect();
        }

        try {
            $features = LandingFeature::where('is_active', true)->orderBy('sort_order')->get();
        } catch (\Exception $e) {
            $features = collect();
        }

        try {
            $stats = LandingStat::active()->ordered()->get();
        } catch (\Exception $e) {
            $stats = collect();
        }

        try {
            $testimonials = LandingTestimonial::active()->ordered()->get();
        } catch (\Exception $e) {
            $testimonials = collect();
        }

        try {
            $faqs = LandingFaq::active()->ordered()->get();
        } catch (\Exception $e) {
            $faqs = collect();
        }

        try {
            $sections = LandingSection::active()->ordered()->get();
        } catch (\Exception $e) {
            $sections = collect();
        }

        // Provide fallback hero slide if database is empty
        if ($slides->isEmpty()) {
            $slides = collect([
                (object)[
                    'title' => config('app.name', 'SMATCAMPUS'),
                    'subtitle' => 'The Complete School Management Solution',
                    'description' => 'Streamline your educational institution with our powerful, easy-to-use platform.',
                    'button_text' => 'Get Started',
                    'button_url' => route('register'),
                    'image_url' => null,
                ]
            ]);
        }

        // Provide fallback features if database is empty
        if ($features->isEmpty()) {
            $features = collect([
                (object)['icon' => 'bi-mortarboard', 'title' => 'Academic Management', 'description' => 'Complete curriculum planning and student tracking.', 'color' => 'primary'],
                (object)['icon' => 'bi-calendar-check', 'title' => 'Attendance Tracking', 'description' => 'Multi-method attendance with real-time reporting.', 'color' => 'success'],
                (object)['icon' => 'bi-graph-up', 'title' => 'Performance Analytics', 'description' => 'Comprehensive grade management and insights.', 'color' => 'info'],
                (object)['icon' => 'bi-people', 'title' => 'Communication Hub', 'description' => 'Connect teachers, students, and parents seamlessly.', 'color' => 'warning'],
            ]);
        }

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
