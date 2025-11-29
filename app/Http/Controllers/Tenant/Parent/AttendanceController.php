<?php

namespace App\Http\Controllers\Tenant\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $parent = $user->parentProfile;
        
        $students = $parent ? $parent->students()
            ->with(['class', 'stream', 'account.attendanceRecords'])
            ->get() : collect([]);

        return view('tenant.parent.attendance.index', compact('students'));
    }
}
