<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Analyze attendance patterns for a class
     */
    public function analyzePatterns(int $classId, string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Overall statistics
        $totalDays = Attendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$start, $end])
            ->distinct('attendance_date')
            ->count('attendance_date');

        $totalRecords = Attendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$start, $end])
            ->count();

        $presentCount = Attendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$start, $end])
            ->where('status', 'present')
            ->count();

        $absentCount = Attendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$start, $end])
            ->where('status', 'absent')
            ->count();

        $lateCount = Attendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$start, $end])
            ->where('status', 'late')
            ->count();

        // Per student analysis
        $studentStats = Attendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$start, $end])
            ->select('student_id', 
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late')
            )
            ->groupBy('student_id')
            ->with('student:id,name,photo')
            ->get()
            ->map(function ($stat) {
                $percentage = $stat->total > 0 ? round(($stat->present / $stat->total) * 100, 2) : 0;
                return [
                    'student' => $stat->student,
                    'total' => $stat->total,
                    'present' => $stat->present,
                    'absent' => $stat->absent,
                    'late' => $stat->late,
                    'percentage' => $percentage,
                    'status' => $this->getAttendanceStatus($percentage),
                ];
            })
            ->sortByDesc('percentage')
            ->values();

        // Day of week patterns
        $dayOfWeekPattern = Attendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$start, $end])
            ->select(
                DB::raw('DAYNAME(attendance_date) as day_name'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent')
            )
            ->groupBy('day_name')
            ->get();

        // Monthly trend
        $monthlyTrend = Attendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$start, $end])
            ->select(
                DB::raw('DATE_FORMAT(attendance_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'overview' => [
                'total_days' => $totalDays,
                'total_records' => $totalRecords,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'late_count' => $lateCount,
                'overall_percentage' => $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 2) : 0,
            ],
            'student_stats' => $studentStats,
            'day_of_week_pattern' => $dayOfWeekPattern,
            'monthly_trend' => $monthlyTrend,
        ];
    }

    /**
     * Generate attendance report
     */
    public function generateReport(int $classId, string $type, string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        switch ($type) {
            case 'summary':
                return $this->generateSummaryReport($classId, $start, $end);
            case 'detailed':
                return $this->generateDetailedReport($classId, $start, $end);
            case 'defaulters':
                return $this->generateDefaultersReport($classId, $start, $end);
            default:
                return [];
        }
    }

    /**
     * Generate summary report
     */
    protected function generateSummaryReport(int $classId, Carbon $start, Carbon $end): array
    {
        $data = Attendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$start, $end])
            ->select('student_id',
                DB::raw('COUNT(*) as total_days'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late')
            )
            ->groupBy('student_id')
            ->with('student:id,name,email,photo')
            ->get()
            ->map(function ($record) {
                $percentage = $record->total_days > 0 ? round(($record->present / $record->total_days) * 100, 2) : 0;
                return [
                    'student' => $record->student,
                    'total_days' => $record->total_days,
                    'present' => $record->present,
                    'absent' => $record->absent,
                    'late' => $record->late,
                    'percentage' => $percentage,
                ];
            });

        return [
            'type' => 'summary',
            'data' => $data,
        ];
    }

    /**
     * Generate detailed report
     */
    protected function generateDetailedReport(int $classId, Carbon $start, Carbon $end): array
    {
        $data = Attendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$start, $end])
            ->with(['student:id,name,email,photo', 'markedBy:id,name'])
            ->orderBy('attendance_date', 'desc')
            ->orderBy('student_id')
            ->get();

        return [
            'type' => 'detailed',
            'data' => $data,
        ];
    }

    /**
     * Generate defaulters report (students with low attendance)
     */
    protected function generateDefaultersReport(int $classId, Carbon $start, Carbon $end, float $threshold = 75.0): array
    {
        $data = Attendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$start, $end])
            ->select('student_id',
                DB::raw('COUNT(*) as total_days'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent')
            )
            ->groupBy('student_id')
            ->having(DB::raw('(present / total_days * 100)'), '<', $threshold)
            ->with('student:id,name,email,phone,photo')
            ->get()
            ->map(function ($record) {
                $percentage = $record->total_days > 0 ? round(($record->present / $record->total_days) * 100, 2) : 0;
                return [
                    'student' => $record->student,
                    'total_days' => $record->total_days,
                    'present' => $record->present,
                    'absent' => $record->absent,
                    'percentage' => $percentage,
                    'risk_level' => $this->getRiskLevel($percentage),
                ];
            })
            ->sortBy('percentage');

        return [
            'type' => 'defaulters',
            'threshold' => $threshold,
            'data' => $data,
        ];
    }

    /**
     * Export report in specified format
     */
    public function exportReport(int $classId, string $type, string $format, string $startDate, string $endDate)
    {
        $reportData = $this->generateReport($classId, $type, $startDate, $endDate);

        if ($format === 'pdf') {
            return $this->exportToPdf($reportData, $classId, $startDate, $endDate);
        } else {
            return $this->exportToExcel($reportData, $classId, $startDate, $endDate);
        }
    }

    /**
     * Export to PDF
     */
    protected function exportToPdf(array $reportData, int $classId, string $startDate, string $endDate)
    {
        // This would use a PDF library like DomPDF or Snappy
        // For now, return a placeholder response
        return response()->json([
            'message' => 'PDF export functionality to be implemented',
            'data' => $reportData
        ]);
    }

    /**
     * Export to Excel
     */
    protected function exportToExcel(array $reportData, int $classId, string $startDate, string $endDate)
    {
        // This would use Laravel Excel package
        // For now, return CSV
        $filename = 'attendance-report-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($reportData) {
            $handle = fopen('php://output', 'w');
            
            if ($reportData['type'] === 'summary' || $reportData['type'] === 'defaulters') {
                fputcsv($handle, ['Student Name', 'Email', 'Total Days', 'Present', 'Absent', 'Late', 'Percentage']);
                foreach ($reportData['data'] as $row) {
                    fputcsv($handle, [
                        $row['student']->name ?? 'N/A',
                        $row['student']->email ?? 'N/A',
                        $row['total_days'] ?? 0,
                        $row['present'] ?? 0,
                        $row['absent'] ?? 0,
                        $row['late'] ?? 0,
                        $row['percentage'] ?? 0,
                    ]);
                }
            } else {
                fputcsv($handle, ['Date', 'Student Name', 'Status', 'Method', 'Marked By', 'Notes']);
                foreach ($reportData['data'] as $row) {
                    fputcsv($handle, [
                        $row->attendance_date->format('Y-m-d'),
                        $row->student->name ?? 'N/A',
                        $row->status,
                        $row->method ?? 'manual',
                        $row->markedBy->name ?? 'N/A',
                        $row->notes ?? '',
                    ]);
                }
            }
            
            fclose($handle);
        }, $filename);
    }

    /**
     * Get attendance status based on percentage
     */
    protected function getAttendanceStatus(float $percentage): string
    {
        if ($percentage >= 90) return 'excellent';
        if ($percentage >= 75) return 'good';
        if ($percentage >= 60) return 'average';
        return 'poor';
    }

    /**
     * Get risk level for defaulters
     */
    protected function getRiskLevel(float $percentage): string
    {
        if ($percentage < 50) return 'critical';
        if ($percentage < 60) return 'high';
        if ($percentage < 75) return 'medium';
        return 'low';
    }
}
