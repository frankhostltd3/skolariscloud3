<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;

class AttendanceReportsController extends Controller
{
    public function index()
    {
        return view('tenant.student.attendance.reports');
    }
}