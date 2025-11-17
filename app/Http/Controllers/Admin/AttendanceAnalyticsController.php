<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        $recordTable = (new AttendanceRecord())->getTable();
        $hasSchoolColumn = Schema::hasColumn($recordTable, 'school_id');
        $hasMethodColumn = Schema::hasColumn($recordTable, 'attendance_method');
        $hasVerificationColumn = Schema::hasColumn($recordTable, 'verification_score');

        $baseQuery = AttendanceRecord::query();
        if ($hasSchoolColumn && $schoolId) {
            $baseQuery->where('school_id', $schoolId);
        }

        $baseMethodQuery = (clone $baseQuery)->whereBetween('created_at', [$startDate, $endDate]);

        if ($hasMethodColumn) {
            $methodStats = (clone $baseMethodQuery)
                ->select('attendance_method', DB::raw('count(*) as total'))
                ->groupBy('attendance_method')
                ->get()
                ->mapWithKeys(fn($item) => [$item->attendance_method => $item->total]);
        } else {
            $total = (clone $baseMethodQuery)->count();
            $methodStats = $total ? collect(['manual' => $total]) : collect();
        }

        // Daily trend data (last 30 days)
        $dailyTrendQuery = (clone $baseQuery)
            ->whereBetween('created_at', [now()->subDays(30), now()]);

        if ($hasMethodColumn) {
            $dailyTrend = $dailyTrendQuery
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    'attendance_method',
                    DB::raw('count(*) as count')
                )
                ->groupBy('date', 'attendance_method')
                ->orderBy('date')
                ->get()
                ->groupBy('date');
        } else {
            $dailyTrend = $dailyTrendQuery
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('count(*) as count')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->groupBy('date')
                ->map(function ($items) {
                    return $items->map(function ($item) {
                        $item->attendance_method = 'manual';
                        return $item;
                    });
                });
        }

        // Status distribution
        $statusStats = (clone $baseQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status => $item->total]);

        // Success rate per method (quality score > 70)
        if ($hasMethodColumn && $hasVerificationColumn) {
            $methodSuccessRate = (clone $baseQuery)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('verification_score')
                ->select(
                    'attendance_method',
                    DB::raw('AVG(verification_score) as avg_score'),
                    DB::raw('count(*) as total')
                )
                ->groupBy('attendance_method')
                ->get();
        } else {
            $methodSuccessRate = collect();
        }

        // Peak hours analysis
        $peakHours = (clone $baseQuery)
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
        $recentScanQuery = AttendanceRecord::with(['student', 'staff']);

        if ($hasSchoolColumn && $schoolId) {
            $recentScanQuery->where('school_id', $schoolId);
        }

        $recentScanQuery
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit(10);

        if ($hasVerificationColumn) {
            $recentScanQuery->whereNotNull('verification_score');
        }

        $recentScans = $recentScanQuery->get();

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

        $recordTable = (new AttendanceRecord())->getTable();
        $hasSchoolColumn = Schema::hasColumn($recordTable, 'school_id');
        $hasMethodColumn = Schema::hasColumn($recordTable, 'attendance_method');
        $hasVerificationColumn = Schema::hasColumn($recordTable, 'verification_score');

        $records = AttendanceRecord::when($hasSchoolColumn && $schoolId, function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
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
                $methodValue = $hasMethodColumn ? ($record->attendance_method ?? 'Manual') : 'Manual';
                $verificationValue = $hasVerificationColumn
                    ? ($record->verification_score ?? 'N/A')
                    : 'N/A';

                fputcsv($file, [
                    $record->created_at->format('Y-m-d H:i:s'),
                    $methodValue,
                    $record->status,
                    $record->student_id ? 'Student' : 'Staff',
                    $verificationValue,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
