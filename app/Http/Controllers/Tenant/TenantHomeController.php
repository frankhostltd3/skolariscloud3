<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\View\View;

class TenantHomeController extends Controller
{
    /**
     * Display the tenant landing page.
     */
    public function index(): View
    {
        $school = app()->bound('currentSchool') ? app('currentSchool') : null;

        if (!$school) {
            abort(404, 'School not found');
        }

        return view('tenant.welcome', compact('school'));
    }
}
