<?php

namespace App\Http\Controllers\Tenant\Modules\HumanResource;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Http\Requests\LeaveRequestRequest;
use App\Services\LeaveFinancialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class LeaveRequestsController extends Controller
{
    protected LeaveFinancialService $financialService;

    public function __construct(LeaveFinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index(): View
    {
        $query = LeaveRequest::with(['employee.department', 'leaveType', 'approver']);

        // Apply filters
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($employee = request('employee')) {
            $query->whereHas('employee', function ($q) use ($employee) {
                $q->where('first_name', 'like', "%$employee%")
                  ->orWhere('last_name', 'like', "%$employee%");
            });
        }

        if ($leaveType = request('leave_type')) {
            $query->whereHas('leaveType', function ($q) use ($leaveType) {
                $q->where('name', 'like', "%$leaveType%")
                  ->orWhere('code', 'like', "%$leaveType%");
            });
        }

        if ($year = request('year')) {
            $query->whereYear('start_date', $year);
        }

        if ($month = request('month')) {
            $query->whereMonth('start_date', $month);
        }

        $leaveRequests = $query->latest()->paginate(20);

        // Calculate summary statistics
        $allRequests = LeaveRequest::query();
        if ($year) $allRequests->whereYear('start_date', $year);
        if ($month) $allRequests->whereMonth('start_date', $month);

        $summary = [
            'total_requests' => $allRequests->count(),
            'pending' => $allRequests->clone()->where('status', 'pending')->count(),
            'approved' => $allRequests->clone()->where('status', 'approved')->count(),
            'total_days' => $allRequests->clone()->where('status', 'approved')->sum('days_requested'),
            'total_financial_impact' => $allRequests->clone()->sum('financial_impact'),
            'unpaid_deductions' => $allRequests->clone()->where('is_paid', false)->where('status', 'approved')->sum('financial_impact'),
        ];

        // Get unique employees, leave types, years for filters
        $employees = Employee::where('employment_status', 'active')->orderBy('first_name')->get();
        $leaveTypes = LeaveType::orderBy('name')->get();
        $years = LeaveRequest::selectRaw('YEAR(start_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('tenant.modules.human_resource.leave_requests.index', compact(
            'leaveRequests',
            'summary',
            'employees',
            'leaveTypes',
            'years'
        ));
    }

    public function create(): View
    {
        $this->authorize('create', LeaveRequest::class);
        $employees = \App\Models\Employee::where('employment_status', 'active')->get();
        $leaveTypes = \App\Models\LeaveType::all();
        return view('tenant.modules.human_resource.leave_requests.create', compact('employees', 'leaveTypes'));
    }

    public function store(LeaveRequestRequest $request): RedirectResponse
    {
        $this->authorize('create', LeaveRequest::class);
        $leaveRequest = LeaveRequest::create($request->validated());
        return redirect()->route('human_resources.leave-requests.index')->with('success', 'Leave request created successfully.');
    }

    public function show(LeaveRequest $leaveRequest): View
    {
        $this->authorize('view', $leaveRequest);
        return view('tenant.modules.human_resource.leave_requests.show', compact('leaveRequest'));
    }

    public function edit(LeaveRequest $leaveRequest): View
    {
        $this->authorize('update', $leaveRequest);
        return view('tenant.modules.human_resource.leave_requests.edit', compact('leaveRequest'));
    }

    public function update(LeaveRequestRequest $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $this->authorize('update', $leaveRequest);

        $leaveRequest->update($request->validated());

        $statusMessage = match($leaveRequest->status) {
            'approved' => 'Leave request approved successfully.',
            'rejected' => 'Leave request rejected.',
            default => 'Leave request updated successfully.'
        };

        return redirect()->route('tenant.modules.human_resources.leave-requests.show', $leaveRequest)
                        ->with('success', __($statusMessage));
    }

    public function destroy(LeaveRequest $leaveRequest): RedirectResponse
    {
        $this->authorize('delete', $leaveRequest);
        $leaveRequest->delete();
        return redirect()->route('human_resources.leave-requests.index')->with('success', 'Leave request deleted successfully.');
    }

    /**
     * Approve a leave request
     */
    public function approve(LeaveRequest $leaveRequest): RedirectResponse
    {
        $this->authorize('update', $leaveRequest);

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Leave request approved successfully.');
    }

    /**
     * Reject a leave request
     */
    public function reject(LeaveRequest $leaveRequest): RedirectResponse
    {
        $this->authorize('update', $leaveRequest);

        $leaveRequest->update([
            'status' => 'declined',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Leave request declined.');
    }

    /**
     * Show financial report
     */
    public function financialReport(): View
    {
        $year = request('year', now()->year);
        $month = request('month');

        $report = $this->financialService->generateFinancialReport($year, $month);

        // Get unpaid leave deductions for payroll integration
        if ($month) {
            $report['unpaid_deductions_detail'] = $this->financialService->getUnpaidLeaveDeductions($year, $month);
        }

        // Get excessive leave usage alerts
        $report['excessive_usage'] = $this->financialService->getExcessiveLeaveUsage(80, $year);

        return view('tenant.modules.human_resource.leave_requests.financial_report', compact('report', 'year', 'month'));
    }

    /**
     * Export financial report to CSV
     */
    public function exportFinancialReport()
    {
        $year = request('year', now()->year);
        $month = request('month');

        $data = $this->financialService->exportFinancialReport($year, $month);

        $filename = 'leave_financial_report_' . $year . ($month ? '_' . str_pad($month, 2, '0', STR_PAD_LEFT) : '') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            if (!empty($data)) {
                fputcsv($file, array_keys($data[0]));
            }

            // Add data
            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show employee leave balance
     */
    public function employeeBalance(?int $employeeId = null): View
    {
        $employeeId = $employeeId ?? auth()->user()?->employee?->id;
        $year = request('year', now()->year);

        if (!$employeeId) {
            abort(404, 'Employee not found');
        }

        $employee = Employee::findOrFail($employeeId);
        $balance = $this->financialService->getEmployeeLeaveBalance($employeeId, $year);

        // Get recent leave history
        $recentLeaves = LeaveRequest::with(['leaveType'])
            ->where('employee_id', $employeeId)
            ->whereYear('start_date', $year)
            ->orderByDesc('start_date')
            ->limit(10)
            ->get();

        return view('tenant.modules.human_resource.leave_requests.employee_balance', compact(
            'employee',
            'balance',
            'recentLeaves',
            'year'
        ));
    }
}
