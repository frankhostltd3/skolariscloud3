<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\PayrollRecord;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LeaveFinancialService
{
    /**
     * Generate comprehensive financial report for leave requests
     */
    public function generateFinancialReport(?int $year = null, ?int $month = null): array
    {
        $year = $year ?? now()->year;
        $query = LeaveRequest::with(['employee', 'leaveType'])
            ->whereYear('start_date', $year);

        if ($month) {
            $query->whereMonth('start_date', $month);
        }

        $leaveRequests = $query->get();

        return [
            'period' => [
                'year' => $year,
                'month' => $month,
                'display' => $month ? Carbon::create($year, $month)->format('F Y') : $year,
            ],
            'summary' => [
                'total_requests' => $leaveRequests->count(),
                'approved_requests' => $leaveRequests->where('status', 'approved')->count(),
                'pending_requests' => $leaveRequests->where('status', 'pending')->count(),
                'total_days' => $leaveRequests->sum('days_requested'),
                'total_financial_impact' => $leaveRequests->sum('financial_impact'),
                'paid_leave_value' => $leaveRequests->where('is_paid', true)->sum('financial_impact'),
                'unpaid_leave_deductions' => $leaveRequests->where('is_paid', false)->sum('financial_impact'),
            ],
            'by_leave_type' => $this->groupByLeaveType($leaveRequests),
            'by_department' => $this->groupByDepartment($leaveRequests),
            'top_requestors' => $this->getTopRequestors($leaveRequests),
            'pending_approvals' => $this->getPendingApprovalsValue($leaveRequests),
        ];
    }

    /**
     * Get unpaid leave deductions for payroll processing
     */
    public function getUnpaidLeaveDeductions(int $year, int $month): Collection
    {
        return LeaveRequest::with(['employee', 'leaveType'])
            ->where('status', 'approved')
            ->where('is_paid', false)
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->get()
            ->map(function ($leave) {
                return [
                    'employee_id' => $leave->employee_id,
                    'employee_name' => $leave->employee->full_name,
                    'leave_type' => $leave->leaveType->name,
                    'days' => $leave->days_requested,
                    'daily_rate' => $leave->daily_rate,
                    'deduction_amount' => $leave->financial_impact,
                    'start_date' => $leave->start_date->format('Y-m-d'),
                    'end_date' => $leave->end_date->format('Y-m-d'),
                    'leave_request_id' => $leave->id,
                ];
            });
    }

    /**
     * Process unpaid leave deductions in payroll
     */
    public function processUnpaidLeaveDeductions(PayrollRecord $payrollRecord): float
    {
        $employee = $payrollRecord->employee;
        $periodYear = Carbon::parse($payrollRecord->period_year . '-' . $payrollRecord->period_month . '-01')->year;
        $periodMonth = Carbon::parse($payrollRecord->period_year . '-' . $payrollRecord->period_month . '-01')->month;

        // Get all unpaid leaves for this employee in the payroll period
        $unpaidLeaves = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('is_paid', false)
            ->whereYear('start_date', $periodYear)
            ->whereMonth('start_date', $periodMonth)
            ->get();

        $totalDeduction = $unpaidLeaves->sum('financial_impact');

        // Add to other_deductions field if it exists
        if ($totalDeduction > 0) {
            $payrollRecord->other_deductions = ($payrollRecord->other_deductions ?? 0) + $totalDeduction;
            $payrollRecord->notes = ($payrollRecord->notes ?? '') . "\n\nUnpaid Leave Deduction: UGX " . number_format($totalDeduction, 2) . " (" . $unpaidLeaves->sum('days_requested') . " days)";
            $payrollRecord->save();
        }

        return $totalDeduction;
    }

    /**
     * Get leave balance summary for employee
     */
    public function getEmployeeLeaveBalance(int $employeeId, ?int $year = null): array
    {
        $year = $year ?? now()->year;
        $balances = LeaveRequest::getAllBalances($employeeId, $year);

        $summary = [
            'employee_id' => $employeeId,
            'year' => $year,
            'total_entitlement' => 0,
            'total_used' => 0,
            'total_remaining' => 0,
            'total_pending' => 0,
            'leave_types' => [],
        ];

        foreach ($balances as $code => $balance) {
            $summary['total_entitlement'] += $balance['entitlement'];
            $summary['total_used'] += $balance['used'];
            $summary['total_remaining'] += $balance['remaining'];
            $summary['total_pending'] += $balance['pending'];
            
            $summary['leave_types'][] = [
                'type' => $balance['leave_type']->name,
                'code' => $code,
                'entitlement' => $balance['entitlement'],
                'used' => $balance['used'],
                'remaining' => $balance['remaining'],
                'pending' => $balance['pending'],
                'available' => $balance['available'],
                'usage_percentage' => $balance['entitlement'] > 0 
                    ? round(($balance['used'] / $balance['entitlement']) * 100, 1) 
                    : 0,
            ];
        }

        return $summary;
    }

    /**
     * Get employees with excessive leave usage
     */
    public function getExcessiveLeaveUsage(float $threshold = 80, ?int $year = null): Collection
    {
        $year = $year ?? now()->year;
        $employees = Employee::where('employment_status', 'active')->get();

        return $employees->map(function ($employee) use ($threshold, $year) {
            $balance = $this->getEmployeeLeaveBalance($employee->id, $year);
            
            if ($balance['total_entitlement'] == 0) {
                return null;
            }

            $usagePercentage = ($balance['total_used'] / $balance['total_entitlement']) * 100;

            if ($usagePercentage >= $threshold) {
                return [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->full_name,
                    'department' => $employee->department?->name,
                    'total_entitlement' => $balance['total_entitlement'],
                    'total_used' => $balance['total_used'],
                    'usage_percentage' => round($usagePercentage, 1),
                    'remaining' => $balance['total_remaining'],
                ];
            }

            return null;
        })->filter()->values();
    }

    /**
     * Export financial report to array for CSV/Excel
     */
    public function exportFinancialReport(int $year, ?int $month = null): array
    {
        $query = LeaveRequest::with(['employee.department', 'leaveType'])
            ->whereYear('start_date', $year);

        if ($month) {
            $query->whereMonth('start_date', $month);
        }

        return $query->get()->map(function ($leave) {
            return [
                'Employee Number' => $leave->employee->employee_number ?? 'N/A',
                'Employee Name' => $leave->employee->full_name,
                'Department' => $leave->employee->department?->name ?? 'N/A',
                'Leave Type' => $leave->leaveType->name,
                'Start Date' => $leave->start_date->format('Y-m-d'),
                'End Date' => $leave->end_date->format('Y-m-d'),
                'Days' => $leave->days_requested,
                'Status' => ucfirst($leave->status),
                'Paid/Unpaid' => $leave->is_paid ? 'Paid' : 'Unpaid',
                'Daily Rate' => number_format($leave->daily_rate, 2),
                'Financial Impact' => number_format($leave->financial_impact, 2),
                'Deduction Required' => !$leave->is_paid && $leave->status === 'approved' ? 'Yes' : 'No',
                'Requested Date' => $leave->created_at->format('Y-m-d'),
                'Approved Date' => $leave->approved_at?->format('Y-m-d') ?? 'N/A',
            ];
        })->toArray();
    }

    /**
     * Group leave requests by leave type
     */
    private function groupByLeaveType(Collection $leaveRequests): array
    {
        return $leaveRequests->groupBy('leaveType.name')->map(function ($group, $type) {
            return [
                'type' => $type,
                'count' => $group->count(),
                'total_days' => $group->sum('days_requested'),
                'financial_impact' => $group->sum('financial_impact'),
            ];
        })->values()->toArray();
    }

    /**
     * Group leave requests by department
     */
    private function groupByDepartment(Collection $leaveRequests): array
    {
        return $leaveRequests->groupBy('employee.department.name')->map(function ($group, $dept) {
            return [
                'department' => $dept ?? 'No Department',
                'count' => $group->count(),
                'total_days' => $group->sum('days_requested'),
                'financial_impact' => $group->sum('financial_impact'),
            ];
        })->values()->toArray();
    }

    /**
     * Get top leave requestors
     */
    private function getTopRequestors(Collection $leaveRequests): array
    {
        return $leaveRequests->groupBy('employee_id')->map(function ($group) {
            $employee = $group->first()->employee;
            return [
                'employee_name' => $employee->full_name,
                'department' => $employee->department?->name,
                'total_requests' => $group->count(),
                'total_days' => $group->sum('days_requested'),
                'financial_impact' => $group->sum('financial_impact'),
            ];
        })->sortByDesc('total_days')->take(10)->values()->toArray();
    }

    /**
     * Get value of pending approvals
     */
    private function getPendingApprovalsValue(Collection $leaveRequests): array
    {
        $pending = $leaveRequests->where('status', 'pending');
        
        return [
            'count' => $pending->count(),
            'total_days' => $pending->sum('days_requested'),
            'potential_impact' => $pending->sum('financial_impact'),
        ];
    }
}
