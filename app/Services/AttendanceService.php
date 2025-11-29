<?php

namespace App\Services;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function analyzePatterns($classId, $startDate, $endDate)
    {
        // Get all records for the class in the date range
        $records = \App\Models\AttendanceRecord::whereHas('attendance', function($q) use ($classId, $startDate, $endDate) {
                $q->where('class_id', $classId)
                  ->whereBetween('attendance_date', [$startDate, $endDate]);
            })
            ->with('attendance')
            ->get();

        // Calculate total instructional days (days where attendance was taken)
        $totalDays = Attendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->count();

        $students = $records->groupBy('student_id');

        $studentStats = [];
        $totalPresent = 0;
        $totalAbsent = 0;
        $totalLate = 0;

        foreach ($students as $studentId => $studentRecords) {
            $present = $studentRecords->where('status', 'present')->count();
            $absent = $studentRecords->where('status', 'absent')->count();
            $late = $studentRecords->where('status', 'late')->count();

            $totalPresent += $present;
            $totalAbsent += $absent;
            $totalLate += $late;

            $percentage = $totalDays > 0 ? ($present / $totalDays) * 100 : 0;

            $student = \App\Models\User::find($studentId);
            if (!$student) continue;

            $studentStats[] = [
                'student' => $student,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'total_days' => $totalDays,
                'percentage' => round($percentage, 1),
                'status_label' => $this->getAttendanceStatusLabel($percentage),
            ];
        }

        // Calculate overall stats
        $overallPercentage = count($studentStats) > 0 ? collect($studentStats)->avg('percentage') : 0;

        // Day of week pattern
        $dayOfWeekPattern = Attendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get()
            ->groupBy(function($attendance) {
                return Carbon::parse($attendance->attendance_date)->format('l');
            })
            ->map(function($dayRecords) {
                $totalRecords = 0;
                $presentRecords = 0;
                foreach ($dayRecords as $attendance) {
                    $stats = $attendance->getStatistics();
                    $totalRecords += $stats['total'] ?? 0;
                    $presentRecords += $stats['present'] ?? 0;
                }
                return $totalRecords > 0 ? round(($presentRecords / $totalRecords) * 100, 1) : 0;
            });

        // Monthly trend
        $monthlyTrend = Attendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get()
            ->groupBy(function($attendance) {
                return Carbon::parse($attendance->attendance_date)->format('Y-m');
            })
            ->map(function($monthRecords) {
                $totalRecords = 0;
                $presentRecords = 0;
                foreach ($monthRecords as $attendance) {
                    $stats = $attendance->getStatistics();
                    $totalRecords += $stats['total'] ?? 0;
                    $presentRecords += $stats['present'] ?? 0;
                }
                return $totalRecords > 0 ? round(($presentRecords / $totalRecords) * 100, 1) : 0;
            });

        return [
            'overview' => [
                'total_days' => $totalDays,
                'present_count' => $totalPresent,
                'absent_count' => $totalAbsent,
                'late_count' => $totalLate,
                'overall_percentage' => round($overallPercentage, 1),
            ],
            'student_stats' => $studentStats,
            'day_of_week_pattern' => $dayOfWeekPattern,
            'monthly_trend' => $monthlyTrend,
        ];
    }

    private function getAttendanceStatusLabel($percentage) {
        if ($percentage >= 90) return 'excellent';
        if ($percentage >= 75) return 'good';
        if ($percentage >= 50) return 'average';
        return 'poor';
    }

    public function generateReport($classId, $reportType, $startDate, $endDate)
    {
        if ($reportType === 'detailed') {
            $records = \App\Models\AttendanceRecord::whereHas('attendance', function($q) use ($classId, $startDate, $endDate) {
                    $q->where('class_id', $classId)
                      ->whereBetween('attendance_date', [$startDate, $endDate]);
                })
                ->with(['attendance.teacher', 'student'])
                ->get()
                ->map(function($record) {
                    $record->attendance_date = $record->attendance->attendance_date;
                    $record->markedBy = $record->attendance->teacher;
                    $record->method = $record->attendance->attendance_method ?? 'manual';
                    return $record;
                });

            return ['data' => $records];
        }

        // For summary and defaulters
        $patterns = $this->analyzePatterns($classId, $startDate, $endDate);
        $studentStats = $patterns['student_stats'];

        $data = [];
        foreach ($studentStats as $stat) {
            $row = [
                'student' => $stat['student'],
                'total_days' => $stat['total_days'],
                'present' => $stat['present'],
                'absent' => $stat['absent'],
                'late' => $stat['late'],
                'percentage' => $stat['percentage'],
            ];

            if ($reportType === 'defaulters') {
                if ($row['percentage'] < 75) {
                    $row['risk_level'] = $row['percentage'] < 50 ? 'critical' : ($row['percentage'] < 65 ? 'high' : 'medium');
                    $data[] = $row;
                }
            } else {
                $data[] = $row;
            }
        }

        return ['data' => $data];
    }

    public function exportReport($classId, $type, $format, $startDate, $endDate)
    {
        return redirect()->back()->with('success', 'Export functionality not yet implemented.');
    }
}
