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
        
        $patterns = [];
        foreach ($students as $studentId => $studentRecords) {
            $present = $studentRecords->where('status', 'present')->count();
            $absent = $studentRecords->where('status', 'absent')->count();
            $late = $studentRecords->where('status', 'late')->count();
            
            $patterns[$studentId] = [
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'attendance_rate' => $totalDays > 0 ? ($present / $totalDays) * 100 : 0,
            ];
        }
        
        return $patterns;
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
        
        // Get total instructional days
        $totalDays = Attendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->count();
            
        $data = [];
        foreach ($patterns as $studentId => $stats) {
            $student = \App\Models\User::find($studentId);
            if (!$student) continue;
            
            $row = [
                'student' => $student,
                'total_days' => $totalDays,
                'present' => $stats['present'],
                'absent' => $stats['absent'],
                'late' => $stats['late'],
                'percentage' => round($stats['attendance_rate'], 1),
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
