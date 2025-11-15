<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $userType = $user->user_type instanceof UserType ? $user->user_type : UserType::from($user->user_type);
        $view = $userType->viewPath();

        abort_unless(view()->exists($view), 404);

        $data = [
            'user' => $user,
            'title' => $userType->label() . ' Dashboard',
            'school' => $request->attributes->get('currentSchool'),
        ];

        // Add admin-specific data
        if ($userType === UserType::ADMIN) {
            $data = array_merge($data, $this->getAdminDashboardData($user));
        }

        return view($view, $data);
    }

    private function getAdminDashboardData($user): array
    {
        $schoolId = $user->school_id;

        // Count active students
        $activeStudentsCount = User::where('school_id', $schoolId)
            ->where('user_type', UserType::STUDENT)
            ->count();

        // Count staff members (teaching + general staff)
        $staffCount = User::where('school_id', $schoolId)
            ->whereIn('user_type', [UserType::TEACHING_STAFF, UserType::GENERAL_STAFF])
            ->count();

        // Placeholder data for metrics
        $recentEnrollments = 0; // TODO: Calculate students enrolled this month
        $attendancePercentage = 0; // TODO: Calculate today's attendance
        $feesOutstanding = 0; // TODO: Calculate outstanding fees
        $totalClasses = 0; // TODO: Count classes

        return [
            'activeStudentsCount' => $activeStudentsCount,
            'staffCount' => $staffCount,
            'recentEnrollments' => $recentEnrollments,
            'attendancePercentage' => $attendancePercentage,
            'feesOutstanding' => $feesOutstanding,
            'totalClasses' => $totalClasses,
            'criticalAlerts' => [], // TODO: Add critical alerts logic
            'weeklyAttendance' => [], // TODO: Add weekly attendance data
            'studentsByClass' => [], // TODO: Add top classes data
            'recentActivities' => [], // TODO: Add recent activities
        ];
    }
}
