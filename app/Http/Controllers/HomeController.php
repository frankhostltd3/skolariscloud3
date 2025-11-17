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
        // Always show the marketing landing page at root
        // Users can navigate to login/register from there
        return view('home');
    }
}
