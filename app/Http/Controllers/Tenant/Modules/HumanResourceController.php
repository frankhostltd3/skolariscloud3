<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\PayrollRecord;
use App\Models\Position;
use App\Models\SalaryScale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HumanResourceController extends Controller
{
    /**
     * Display the human resource module dashboard with live metrics
     */
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $headcount = [
            'total' => Employee::count(),
            'active' => Employee::where('employment_status', 'active')->count(),
            'probation' => Employee::where('employment_status', 'probation')->count(),
            'on_leave' => Employee::where('employment_status', 'on_leave')->count(),
            'recent_hires' => Employee::whereDate('hire_date', '>=', $today->copy()->subDays(30))->count(),
            'average_tenure_months' => $this->calculateAverageTenureMonths($today),
            'teachers' => Employee::where('is_teacher', true)->count(),
        ];

        $departmentLeaders = Department::withCount('employees')
            ->orderByDesc('employees_count')
            ->limit(5)
            ->get();

        $statusDistribution = Employee::select('employment_status as status', DB::raw('COUNT(*) as total'))
            ->groupBy('employment_status')
            ->orderByDesc('total')
            ->get();

        $genderDistribution = Schema::hasColumn('employees', 'gender')
            ? Employee::select('gender', DB::raw('COUNT(*) as total'))
                ->groupBy('gender')
                ->get()
            : collect();

        $leaveSummary = Schema::hasTable('leave_requests')
            ? [
                'pending' => LeaveRequest::where('status', 'pending')->count(),
                'in_progress' => LeaveRequest::where('status', 'approved')
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today)
                    ->count(),
                'approved_this_month' => LeaveRequest::where('status', 'approved')
                    ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                    ->count(),
                'financial_impact_month' => LeaveRequest::whereBetween('start_date', [$startOfMonth, $endOfMonth])
                    ->sum('financial_impact'),
                'top_types' => LeaveRequest::select('leave_type_id', DB::raw('COUNT(*) as total'))
                    ->with('leaveType:id,name')
                    ->groupBy('leave_type_id')
                    ->orderByDesc('total')
                    ->limit(3)
                    ->get(),
            ]
            : [
                'pending' => 0,
                'in_progress' => 0,
                'approved_this_month' => 0,
                'financial_impact_month' => 0,
                'top_types' => collect(),
            ];

        $payrollSummary = Schema::hasTable('payroll_records')
            ? [
                'current_month_net' => PayrollRecord::where('period_month', $today->month)
                    ->where('period_year', $today->year)
                    ->sum('net_salary'),
                'pending_runs' => PayrollRecord::whereIn('status', ['pending', 'approved'])->count(),
                'last_run' => PayrollRecord::orderByDesc('payment_date')->first(),
            ]
            : [
                'current_month_net' => 0,
                'pending_runs' => 0,
                'last_run' => null,
            ];

        $recentHires = Employee::with('department')
            ->orderByDesc('hire_date')
            ->limit(5)
            ->get();

        $upcomingBirthdays = $this->getUpcomingBirthdays($today);

        $structureTotals = [
            'departments' => Department::count(),
            'positions' => Position::count(),
            'salary_scales' => SalaryScale::count(),
        ];

        return view('tenant.modules.human_resource.index', compact(
            'headcount',
            'departmentLeaders',
            'leaveSummary',
            'payrollSummary',
            'recentHires',
            'upcomingBirthdays',
            'structureTotals',
            'statusDistribution',
            'genderDistribution'
        ));
    }

    private function calculateAverageTenureMonths(Carbon $today): float
    {
        $employees = Employee::whereNotNull('hire_date')->get(['hire_date']);

        if ($employees->isEmpty()) {
            return 0;
        }

        $average = $employees->avg(function ($employee) use ($today) {
            return $employee->hire_date?->diffInMonths($today) ?? 0;
        });

        return round($average, 1);
    }

    private function getUpcomingBirthdays(Carbon $today)
    {
        return Employee::with('department')
            ->select('id', 'first_name', 'last_name', 'birth_date', 'department_id')
            ->whereNotNull('birth_date')
            ->get()
            ->map(function ($employee) use ($today) {
                $nextBirthday = $employee->birth_date->copy()->year($today->year);

                if ($nextBirthday->isBefore($today)) {
                    $nextBirthday->addYear();
                }

                $employee->next_birthday = $nextBirthday;
                $employee->days_until_birthday = $today->diffInDays($nextBirthday);

                return $employee;
            })
            ->filter(fn ($employee) => $employee->days_until_birthday <= 30)
            ->sortBy('next_birthday')
            ->values()
            ->take(5);
    }
}
