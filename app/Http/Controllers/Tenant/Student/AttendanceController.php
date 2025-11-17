<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display the student's attendance page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $studentId = $user->id;

        // Filters
        $status = $request->input('status', 'all');
        $dateRange = $request->input('date_range', 'this_term');

        // Date range logic
        $today = Carbon::today();
        $termStart = Carbon::parse($request->input('term_start', $today->copy()->startOfMonth()));
        $termEnd = Carbon::parse($request->input('term_end', $today->copy()->endOfMonth()));
        if ($dateRange === 'this_month') {
            $startDate = $today->copy()->startOfMonth();
            $endDate = $today->copy()->endOfMonth();
        } elseif ($dateRange === 'custom') {
            $startDate = Carbon::parse($request->input('start_date', $termStart));
            $endDate = Carbon::parse($request->input('end_date', $termEnd));
        } else { // default: this_term
            $startDate = $termStart;
            $endDate = $termEnd;
        }

        $query = Attendance::forStudent($studentId)->dateRange($startDate, $endDate)->orderByDesc('attendance_date');
        if ($status !== 'all') {
            $query->status($status);
        }
        $records = $query->get();

        // Summary
        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $late = $records->where('status', 'late')->count();
        $excused = $records->where('status', 'excused')->count();
        $percentPresent = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        return view('tenant.student.attendance.index', [
            'records' => $records,
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'percentPresent' => $percentPresent,
            'status' => $status,
            'dateRange' => $dateRange,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
        ]);
    }
}