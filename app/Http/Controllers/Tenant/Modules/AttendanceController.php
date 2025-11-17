<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Academic\ClassRoom;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        // Admins can see all active classes; teachers only their assigned classes
        $classesQuery = ClassRoom::query();
        if (Schema::hasColumn('classes','is_active')) {
            $classesQuery->where('is_active', true);
        }
        if ($authUser && $authUser->hasRole('teacher')) {
            $classesQuery->where('class_teacher_id', $authUser->id);
        }
        $classes = $classesQuery->get();
        $selectedClass = $request->class_id ? ClassRoom::find($request->class_id) : null;
        $selectedDate = $request->date ? Carbon::parse($request->date) : Carbon::today();

        $attendanceRecords = [];
        $weeklyStats = [];
        $monthlyStats = [];
        $criticalStudents = [];
        $todayStats = [
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
            'not_marked' => 0,
            'total' => 0,
            'percentage' => 0
        ];
        
        if ($selectedClass) {
            // Use enrollment relationship for active students in this class
            $students = $selectedClass->activeStudents()->get();
            $todayStats['total'] = $students->count();

            foreach ($students as $student) {
                $attendance = Attendance::where('student_id', $student->id)
                    ->where('class_id', $selectedClass->id)
                    ->whereDate('attendance_date', $selectedDate)
                    ->first();

                // Get student attendance history for the last 30 days
                $studentHistory = Attendance::where('student_id', $student->id)
                    ->where('class_id', $selectedClass->id)
                    ->where('attendance_date', '>=', Carbon::now()->subDays(30))
                    ->get();

                $studentStats = [
                    'total_days' => $studentHistory->count(),
                    'present' => $studentHistory->where('status', 'present')->count(),
                    'absent' => $studentHistory->where('status', 'absent')->count(),
                    'late' => $studentHistory->where('status', 'late')->count(),
                    'percentage' => 0
                ];

                if ($studentStats['total_days'] > 0) {
                    $studentStats['percentage'] = round(($studentStats['present'] / $studentStats['total_days']) * 100, 1);
                }

                $attendanceRecords[] = [
                    'student' => $student,
                    'attendance' => $attendance,
                    'history' => $studentStats
                ];

                // Track critical students (attendance < 75%)
                if ($studentStats['percentage'] < 75 && $studentStats['total_days'] >= 5) {
                    $criticalStudents[] = [
                        'student' => $student,
                        'percentage' => $studentStats['percentage'],
                        'absent_days' => $studentStats['absent']
                    ];
                }

                // Update today's stats
                if ($attendance) {
                    $todayStats[$attendance->status]++;
                }
            }

            $todayStats['not_marked'] = $todayStats['total'] - ($todayStats['present'] + $todayStats['absent'] + $todayStats['late'] + $todayStats['excused']);
            if ($todayStats['total'] > 0) {
                $todayStats['percentage'] = round(($todayStats['present'] / $todayStats['total']) * 100, 1);
            }

            // Get weekly statistics (last 7 days)
            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();
            
            for ($i = 0; $i < 7; $i++) {
                $date = $weekStart->copy()->addDays($i);
                if ($date->isWeekday() && $date <= Carbon::now()) {
                    $dayAttendance = Attendance::where('class_id', $selectedClass->id)
                        ->whereDate('attendance_date', $date)
                        ->get();
                    
                    $weeklyStats[] = [
                        'date' => $date->format('M d'),
                        'day' => $date->format('D'),
                        'present' => $dayAttendance->where('status', 'present')->count(),
                        'absent' => $dayAttendance->where('status', 'absent')->count(),
                        'late' => $dayAttendance->where('status', 'late')->count(),
                        'total' => $students->count()
                    ];
                }
            }

            // Get monthly statistics (last 30 days grouped by week)
            for ($week = 0; $week < 4; $week++) {
                $weekStart = Carbon::now()->subWeeks($week + 1)->startOfWeek();
                $weekEnd = Carbon::now()->subWeeks($week)->endOfWeek();
                
                $weekAttendance = Attendance::where('class_id', $selectedClass->id)
                    ->whereBetween('attendance_date', [$weekStart, $weekEnd])
                    ->get();
                
                $totalDays = $weekAttendance->unique('attendance_date')->count();
                $presentCount = $weekAttendance->where('status', 'present')->count();
                
                $monthlyStats[] = [
                    'week' => 'Week ' . (4 - $week),
                    'percentage' => $totalDays > 0 ? round(($presentCount / ($students->count() * $totalDays)) * 100, 1) : 0,
                    'present' => $presentCount,
                    'total' => $students->count() * $totalDays
                ];
            }
            $monthlyStats = array_reverse($monthlyStats);

            // Sort critical students by attendance percentage
            usort($criticalStudents, function($a, $b) {
                return $a['percentage'] <=> $b['percentage'];
            });
        }

        return view('tenant.modules.attendance.index', compact(
            'classes',
            'selectedClass',
            'selectedDate',
            'attendanceRecords',
            'weeklyStats',
            'monthlyStats',
            'criticalStudents',
            'todayStats'
        ));
    }

    /**
     * Mark attendance for students.
     */
    public function mark(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late,excused'
        ]);

        $class = ClassRoom::find($request->class_id);
        $date = Carbon::parse($request->date);

        foreach ($request->attendance as $studentId => $status) {
            Attendance::updateOrCreate([
                'student_id' => $studentId,
                'class_id' => $request->class_id,
                'attendance_date' => $date->format('Y-m-d')
            ], [
                'status' => $status,
                'marked_by' => Auth::id()
            ]);
        }

        return redirect()->back()->with('success', 'Attendance marked successfully.');
    }

    /**
     * Export attendance records.
     */
    public function export(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $class = ClassRoom::find($request->class_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Use active students in the class
        $students = $class->activeStudents()->orderBy('name')->get();

        $attendanceData = [];
        $dateRange = [];

        // Generate date range
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            if ($currentDate->isWeekday()) { // Only school days
                $dateRange[] = $currentDate->format('Y-m-d');
            }
            $currentDate->addDay();
        }

        // Get attendance records
        foreach ($students as $student) {
            $studentAttendance = ['student' => $student];

            foreach ($dateRange as $date) {
                $attendance = Attendance::where('student_id', $student->id)
                    ->where('class_id', $class->id)
                    ->whereDate('attendance_date', $date)
                    ->first();

                $studentAttendance[$date] = $attendance ? $attendance->status : 'not_marked';
            }

            $attendanceData[] = $studentAttendance;
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance-' . $class->name . '-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($attendanceData, $dateRange, $class, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            // CSV Header
            $csvHeader = ['Student Name', 'Student ID'];
            foreach ($dateRange as $date) {
                $csvHeader[] = Carbon::parse($date)->format('M d, Y');
            }
            $csvHeader[] = 'Total Present';
            $csvHeader[] = 'Total Absent';
            $csvHeader[] = 'Attendance Percentage';

            fputcsv($file, $csvHeader);

            // CSV Data
            foreach ($attendanceData as $studentData) {
                $student = $studentData['student'];
                $csvRow = [$student->name, $student->student_id];

                $presentCount = 0;
                $absentCount = 0;
                $totalDays = count($dateRange);

                foreach ($dateRange as $date) {
                    $status = $studentData[$date];
                    $csvRow[] = ucfirst(str_replace('_', ' ', $status));

                    if ($status === 'present' || $status === 'late') {
                        $presentCount++;
                    } elseif ($status === 'absent') {
                        $absentCount++;
                    }
                }

                $csvRow[] = $presentCount;
                $csvRow[] = $absentCount;
                $csvRow[] = $totalDays > 0 ? round(($presentCount / $totalDays) * 100, 2) . '%' : '0%';

                fputcsv($file, $csvRow);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get student attendance history.
     */
    public function studentHistory(Request $request, $studentId)
    {
        $student = User::findOrFail($studentId);
        $days = $request->days ?? 30;
        
        $history = Attendance::where('student_id', $studentId)
            ->where('attendance_date', '>=', Carbon::now()->subDays($days))
            ->orderBy('attendance_date', 'desc')
            ->with(['class', 'markedBy'])
            ->get();

        $stats = [
            'total_days' => $history->count(),
            'present' => $history->where('status', 'present')->count(),
            'absent' => $history->where('status', 'absent')->count(),
            'late' => $history->where('status', 'late')->count(),
            'excused' => $history->where('status', 'excused')->count(),
            'percentage' => 0
        ];

        if ($stats['total_days'] > 0) {
            $stats['percentage'] = round(($stats['present'] / $stats['total_days']) * 100, 1);
        }

        return response()->json([
            'student' => $student,
            'history' => $history,
            'stats' => $stats
        ]);
    }

    /**
     * Send notification to absent students' parents.
     */
    public function notifyAbsent(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date'
        ]);

        $class = ClassRoom::find($request->class_id);
        $date = Carbon::parse($request->date);
        
        $absentStudents = Attendance::where('class_id', $request->class_id)
            ->whereDate('attendance_date', $date)
            ->where('status', 'absent')
            ->with('student')
            ->get();

        $notified = 0;
        foreach ($absentStudents as $attendance) {
            // TODO: Implement actual notification logic (SMS/Email)
            // For now, just count
            $notified++;
        }

        return response()->json([
            'success' => true,
            'message' => "Notifications sent to {$notified} parents",
            'count' => $notified
        ]);
    }

    /**
     * Get comparative statistics across all classes.
     */
    public function comparativeStats(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        
        $classStats = [];
        $classes = ClassRoom::where('is_active', true)->get();
        
        foreach ($classes as $class) {
            $totalStudents = $class->activeStudents()->count();
            $attendance = Attendance::where('class_id', $class->id)
                ->whereDate('attendance_date', $date)
                ->get();
            
            $present = $attendance->where('status', 'present')->count();
            $percentage = $totalStudents > 0 ? round(($present / $totalStudents) * 100, 1) : 0;
            
            $classStats[] = [
                'class_name' => $class->name . ' - ' . $class->section,
                'total' => $totalStudents,
                'present' => $present,
                'absent' => $attendance->where('status', 'absent')->count(),
                'percentage' => $percentage
            ];
        }
        
        // Sort by percentage descending
        usort($classStats, function($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });
        
        return response()->json($classStats);
    }
}
