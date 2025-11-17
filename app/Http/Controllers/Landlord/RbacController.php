<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class RbacController extends Controller
{
    public function __invoke(): View
    {
        return view('landlord.rbac.index');
    }
}
