<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceAnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Date range filter
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Method-wise statistics
        $methodStats = AttendanceRecord::where('school_id', $schoolId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('attendance_method', DB::raw('count(*) as total'))
            ->groupBy('attendance_method')
            ->get()
            ->mapWithKeys(fn($item) => [$item->attendance_method => $item->total]);

        // Daily trend data (last 30 days)
        $dailyTrend = AttendanceRecord::where('school_id', $schoolId)
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->select(
                DB::raw('DATE(created_at) as date'),
                'attendance_method',
                DB::raw('count(*) as count')
            )
            ->groupBy('date', 'attendance_method')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        // Status distribution
        $statusStats = AttendanceRecord::where('school_id', $schoolId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status => $item->total]);

        // Success rate per method (quality score > 70)
        $methodSuccessRate = AttendanceRecord::where('school_id', $schoolId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('verification_score')
            ->select(
                'attendance_method',
                DB::raw('AVG(verification_score) as avg_score'),
                DB::raw('count(*) as total')
            )
            ->groupBy('attendance_method')
            ->get();

        // Peak hours analysis
        $peakHours = AttendanceRecord::where('school_id', $schoolId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('count(*) as count')
            )
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Recent high-quality scans
        $recentScans = AttendanceRecord::with(['student', 'staff'])
            ->where('school_id', $schoolId)
            ->whereNotNull('verification_score')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.attendance.analytics', compact(
            'methodStats',
            'dailyTrend',
            'statusStats',
            'methodSuccessRate',
            'peakHours',
            'recentScans',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        // CSV export logic here
        $schoolId = auth()->user()->school_id;
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $records = AttendanceRecord::where('school_id', $schoolId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $filename = "attendance-analytics-{$startDate}-to-{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Method', 'Status', 'User Type', 'Verification Score']);

            foreach ($records as $record) {
                fputcsv($file, [
                    $record->created_at->format('Y-m-d H:i:s'),
                    $record->attendance_method,
                    $record->status,
                    $record->student_id ? 'Student' : 'Staff',
                    $record->verification_score ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
