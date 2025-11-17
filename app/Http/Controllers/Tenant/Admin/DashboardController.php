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
        try {
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
        } catch (\Exception $e) {
            // Employees table doesn't exist yet, fallback to Staff role
            try {
                $staffCount = User::role('Staff')->count();
            } catch (\Exception $e2) {
                // Role doesn't exist either, use 0
                $staffCount = 0;
            }
        }

        // Calculate today's attendance percentage
        $today = Carbon::today();
        $totalStudents = $activeStudentsCount;
        
        // Count present students from attendance_records for today
        $presentToday = 0;
        try {
            if (DB::getSchemaBuilder()->hasTable('attendance_records')) {
                $presentToday = DB::table('attendance_records')
                    ->join('attendance', 'attendance_records.attendance_id', '=', 'attendance.id')
                    ->whereDate('attendance.attendance_date', $today)
                    ->where('attendance_records.status', 'present')
                    ->distinct('attendance_records.student_id')
                    ->count('attendance_records.student_id');
            }
        } catch (\Exception $e) {
            // Table doesn't exist yet
        }

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
        try {
            if (DB::getSchemaBuilder()->hasTable('attendance_records')) {
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::today()->subDays($i);
                    
                    // Count total attendance records for the day
                    $dayTotal = DB::table('attendance_records')
                        ->join('attendance', 'attendance_records.attendance_id', '=', 'attendance.id')
                        ->whereDate('attendance.attendance_date', $date)
                        ->distinct('attendance_records.student_id')
                        ->count('attendance_records.student_id');
                    
                    // Count present students for the day
                    $dayPresent = DB::table('attendance_records')
                        ->join('attendance', 'attendance_records.attendance_id', '=', 'attendance.id')
                        ->whereDate('attendance.attendance_date', $date)
                        ->where('attendance_records.status', 'present')
                        ->distinct('attendance_records.student_id')
                        ->count('attendance_records.student_id');

                    $weeklyAttendance[] = [
                        'date' => $date->format('M d'),
                        'day' => $date->format('D'),
                        'percentage' => $dayTotal > 0 ? round(($dayPresent / $dayTotal) * 100, 1) : 0,
                        'present' => $dayPresent,
                        'total' => $dayTotal
                    ];
                }
            }
        } catch (\Exception $e) {
            // Tables don't exist yet, return empty array
        }

        // Get recent enrollments (last 30 days)
        $recentEnrollments = User::role('Student')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        // Get students by class distribution
        $studentsByClass = [];
        try {
            // Students are users with Student role, linked via class_id
            $studentsByClass = ClassRoom::select('classes.*')
                ->selectSub(function ($query) {
                    $query->from('users')
                        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->whereColumn('users.class_id', 'classes.id')
                        ->where('roles.name', 'Student')
                        ->where('model_has_roles.model_type', 'App\\Models\\User')
                        ->selectRaw('COUNT(*)');
                }, 'students_count')
                ->orderByDesc('students_count')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            // Tables don't exist yet
        }

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

        // Recent attendance records (absent students)
        try {
            if (DB::getSchemaBuilder()->hasTable('attendance_records')) {
                $recentAbsences = DB::table('attendance_records')
                    ->join('attendance', 'attendance_records.attendance_id', '=', 'attendance.id')
                    ->join('users', 'attendance_records.student_id', '=', 'users.id')
                    ->leftJoin('classes', 'attendance.class_id', '=', 'classes.id')
                    ->where('attendance_records.status', 'absent')
                    ->orderBy('attendance_records.created_at', 'desc')
                    ->limit(3)
                    ->select(
                        'users.name as student_name',
                        'classes.name as class_name',
                        'attendance_records.created_at'
                    )
                    ->get();

                foreach ($recentAbsences as $absence) {
                    $recentActivities[] = [
                        'type' => 'attendance',
                        'message' => __(':student was absent from :class', [
                            'student' => $absence->student_name,
                            'class' => $absence->class_name ?? 'class'
                        ]),
                        'time' => Carbon::parse($absence->created_at),
                        'icon' => 'calendar-x',
                        'color' => 'danger'
                    ];
                }
            }
        } catch (\Exception $e) {
            // Tables don't exist yet, skip
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
        $lowAttendanceStudents = 0;
        try {
            if (DB::getSchemaBuilder()->hasTable('attendance_records')) {
                $lowAttendanceStudents = DB::table('attendance_records')
                    ->join('attendance', 'attendance_records.attendance_id', '=', 'attendance.id')
                    ->select('attendance_records.student_id', 
                             DB::raw('COUNT(*) as total'),
                             DB::raw('SUM(CASE WHEN attendance_records.status = "present" THEN 1 ELSE 0 END) as present'))
                    ->where('attendance.attendance_date', '>=', Carbon::now()->subDays(30))
                    ->groupBy('attendance_records.student_id')
                    ->havingRaw('(present / total * 100) < 75')
                    ->count();
            }
        } catch (\Exception $e) {
            // Tables don't exist yet
        }

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
