<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class SystemHealthController extends Controller
{
    public function __invoke(): View
    {
        return view('landlord.health.index');
    }
}
