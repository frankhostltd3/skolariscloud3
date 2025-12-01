<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = LandingPage::where('slug', $slug)
            ->where('is_published', true)
            ->where('is_active', true)
            ->firstOrFail();

        return view('page', compact('page'));
    }
}
