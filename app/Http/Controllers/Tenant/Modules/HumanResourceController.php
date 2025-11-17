<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HumanResourceController extends Controller
{
    /**
     * Display the human resource module dashboard
     */
    public function index()
    {
        return view('tenant.modules.human_resource.index');
    }
}
