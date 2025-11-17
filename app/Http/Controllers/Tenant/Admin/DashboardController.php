<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Academic\ClassRoom;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Get active students count
        $activeStudentsCount = User::role('Student')->count();
        
        // Get staff members count (employees first, then users with Staff role)
        $staffCount = Employee::count();
        if ($staffCount === 0) {
            // Fallback to users with Staff role if Employee model is empty
            try {
                $staffCount = User::role('Staff')->count();
            } catch (\Exception $e) {
                // Role doesn't exist, use 0
                $staffCount = 0;
            }
        }
        
        // Calculate today's attendance percentage
        $today = Carbon::today();
        $totalStudents = $activeStudentsCount;
        $presentToday = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'present')
            ->count();
        
        $attendancePercentage = $totalStudents > 0 
            ? round(($presentToday / $totalStudents) * 100, 1)
            : 0;
        
        // Get total classes
        $totalClasses = ClassRoom::count();
        
        // Get fees outstanding (if available)
        $feesOutstanding = 0;
        try {
            if (DB::getSchemaBuilder()->hasTable('invoices')) {
                $feesOutstanding = DB::table('invoices')
                    ->where('status', '!=', 'paid')
                    ->sum('amount_due');
            }
        } catch (\Exception $e) {
            // Table doesn't exist, keep default 0
        }
        
        // Get recent attendance stats (last 7 days)
        $weeklyAttendance = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayTotal = Attendance::whereDate('attendance_date', $date)->count();
            $dayPresent = Attendance::whereDate('attendance_date', $date)
                ->where('status', 'present')
                ->count();
            
            $weeklyAttendance[] = [
                'date' => $date->format('M d'),
                'day' => $date->format('D'),
                'percentage' => $dayTotal > 0 ? round(($dayPresent / $dayTotal) * 100, 1) : 0,
                'present' => $dayPresent,
                'total' => $dayTotal
            ];
        }
        
        // Get recent enrollments (last 30 days)
        $recentEnrollments = User::role('Student')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();
        
        // Get students by class distribution
        $studentsByClass = ClassRoom::withCount(['activeStudents'])
            ->orderBy('active_students_count', 'desc')
            ->limit(5)
            ->get();
        
        // Get recent activities (last 10 activities from various sources)
        $recentActivities = [];
        
        // Recent student enrollments
        $newStudents = User::role('Student')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        foreach ($newStudents as $student) {
            $recentActivities[] = [
                'type' => 'enrollment',
                'message' => __('New student enrolled: :name', ['name' => $student->name]),
                'time' => $student->created_at,
                'icon' => 'person-plus',
                'color' => 'success'
            ];
        }
        
        // Recent attendance records
        $recentAttendance = Attendance::with(['student', 'class'])
            ->where('status', 'absent')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        foreach ($recentAttendance as $attendance) {
            if ($attendance->student) {
                $recentActivities[] = [
                    'type' => 'attendance',
                    'message' => __(':student was absent from :class', [
                        'student' => $attendance->student->name,
                        'class' => $attendance->class ? $attendance->class->name : 'class'
                    ]),
                    'time' => $attendance->created_at,
                    'icon' => 'calendar-x',
                    'color' => 'danger'
                ];
            }
        }
        
        // Recent staff additions (check for employees or staff-role users)
        $newStaff = collect();
        
        // Try to get recent employees
        if (Employee::count() > 0) {
            $recentEmployees = Employee::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get();
            
            foreach ($recentEmployees as $employee) {
                if ($employee->user) {
                    $newStaff->push($employee->user);
                }
            }
        } else {
            // Fallback: get users with Staff role if it exists
            try {
                $newStaff = User::role('Staff')
                    ->orderBy('created_at', 'desc')
                    ->limit(2)
                    ->get();
            } catch (\Exception $e) {
                // Role doesn't exist, skip
            }
        }
        
        foreach ($newStaff as $staff) {
            $recentActivities[] = [
                'type' => 'staff',
                'message' => __('New staff member: :name', ['name' => $staff->name]),
                'time' => $staff->created_at,
                'icon' => 'person-badge',
                'color' => 'primary'
            ];
        }
        
        // Sort activities by time
        usort($recentActivities, function($a, $b) {
            return $b['time'] <=> $a['time'];
        });
        
        // Limit to 10 most recent
        $recentActivities = array_slice($recentActivities, 0, 10);
        
        // Get critical alerts
        $criticalAlerts = [];
        
        // Check for low attendance
        if ($attendancePercentage < 75) {
            $criticalAlerts[] = [
                'type' => 'warning',
                'message' => __('Today\'s attendance is below 75%'),
                'action' => route('tenant.modules.attendance.index'),
                'action_text' => __('View Attendance')
            ];
        }
        
        // Check for students with low attendance
        $lowAttendanceStudents = DB::table('attendances')
            ->select('student_id', DB::raw('COUNT(*) as total'), 
                     DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'))
            ->where('attendance_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('student_id')
            ->havingRaw('(present / total * 100) < 75')
            ->count();
        
        if ($lowAttendanceStudents > 0) {
            $criticalAlerts[] = [
                'type' => 'danger',
                'message' => __(':count student(s) have attendance below 75%', ['count' => $lowAttendanceStudents]),
                'action' => route('tenant.modules.attendance.index'),
                'action_text' => __('View Details')
            ];
        }
        
        return view('tenant.admin.dashboard', compact(
            'activeStudentsCount',
            'staffCount',
            'attendancePercentage',
            'totalClasses',
            'feesOutstanding',
            'weeklyAttendance',
            'recentEnrollments',
            'studentsByClass',
            'recentActivities',
            'criticalAlerts'
        ));
    }
}
