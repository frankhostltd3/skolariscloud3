<?php

namespace App\Http\Controllers\Tenant\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        return view('tenant.reports.index');
    }

    public function financial()
    {
        // Logic for financial report
        return view('tenant.reports.financial');
    }

    public function attendance()
    {
        // Logic for attendance report
        return view('tenant.reports.attendance');
    }

    public function enrollment()
    {
        return view('tenant.reports.enrollment');
    }

    public function academic()
    {
        // Logic for academic report
        return view('tenant.reports.academic');
    }

    public function lateSubmissions()
    {
        return view('tenant.reports.late-submissions');
    }
}
