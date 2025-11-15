<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        // If visiting a school-specific subdomain and not authenticated, go to login
        if ($school instanceof School && ! auth()->check()) {
            return redirect()->route('login');
        }

        // If visiting a school subdomain and authenticated, go to dashboard
        if ($school instanceof School && auth()->check()) {
            return redirect()->route('dashboard');
        }

        // Otherwise show the marketing landing page
        return view('home');
    }
}
