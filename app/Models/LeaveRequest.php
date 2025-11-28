<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'days_requested',
        'status',
        'reason',
        'manager_comment',
        'daily_rate',
        'financial_impact',
        'is_paid',
        'financial_notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'days_requested' => 'integer',
        'daily_rate' => 'decimal:2',
        'financial_impact' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Calculate employee's daily salary rate
     */
    public function calculateDailyRate(): float
    {
        if (!$this->employee) {
            return 0;
        }

        $monthlySalary = $this->getEmployeeMonthlySalary();
        return round($monthlySalary / 22, 2); // 22 working days per month
    }

    /**
     * Get employee's monthly salary from salary scale or payroll records
     */
    private function getEmployeeMonthlySalary(): float
    {
        // Try to get from latest payroll record first (most accurate)
        $latestPayroll = $this->employee->payrollRecords()
            ->where('status', 'paid')
            ->latest('payment_date')
            ->first();

        if ($latestPayroll) {
            return (float) $latestPayroll->basic_salary;
        }

        // Fall back to salary scale
        if ($this->employee->salaryScale) {
            if ($this->employee->salaryScale->min_amount && $this->employee->salaryScale->max_amount) {
                return ($this->employee->salaryScale->min_amount + $this->employee->salaryScale->max_amount) / 2;
            }
            return $this->employee->salaryScale->max_amount ?? $this->employee->salaryScale->min_amount ?? 800000;
        }

        return 800000; // Default
    }

    /**
     * Calculate financial impact of this leave request
     */
    public function calculateFinancialImpact(): float
    {
        if (!$this->days_requested || !$this->daily_rate) {
            return 0;
        }

        $impact = $this->daily_rate * $this->days_requested;

        // For unpaid leave, this is a deduction (negative impact)
        // For paid leave, this is the value of paid time off
        return round($impact, 2);
    }

    /**
     * Get formatted financial impact with context
     */
    public function getFinancialImpactDisplayAttribute(): string
    {
        if (!$this->financial_impact) {
            return 'N/A';
        }

        $formatted = 'UGX ' . number_format($this->financial_impact, 2);

        if ($this->is_paid) {
            return $formatted . ' (Paid)';
        }

        return '-' . $formatted . ' (Deduction)';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'approved' => 'bg-success',
            'pending' => 'bg-warning',
            'declined' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    /**
     * Check if request is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if request is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Calculate the number of working days between start and end dates
     */
    public function calculateWorkingDays(): int
    {
        $startDate = $this->start_date;
        $endDate = $this->end_date;

        if (!$startDate || !$endDate || $startDate > $endDate) {
            return 0;
        }

        $days = 0;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // Skip weekends (0 = Sunday, 6 = Saturday)
            if ($currentDate->dayOfWeek !== 0 && $currentDate->dayOfWeek !== 6) {
                $days++;
            }
            $currentDate->addDay();
        }

        return $days;
    }

    /**
     * Get remaining leave balance for employee by leave type
     */
    public static function getRemainingBalance(int $employeeId, int $leaveTypeId, ?int $year = null): array
    {
        $year = $year ?? now()->year;
        $leaveType = LeaveType::find($leaveTypeId);

        $entitlement = $leaveType ? ($leaveType->annual_entitlement ?? $leaveType->default_days ?? 0) : 0;

        if ($entitlement === 0) {
            return [
                'entitlement' => 0,
                'used' => 0,
                'remaining' => 0,
                'pending' => 0,
                'available' => 0,
            ];
        }

        // Calculate used days (approved only)
        $usedDays = static::where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->sum('days_requested');

        // Calculate pending days
        $pendingDays = static::where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', 'pending')
            ->whereYear('start_date', $year)
            ->sum('days_requested');

        $remaining = max(0, $entitlement - $usedDays);

        return [
            'entitlement' => $entitlement,
            'used' => $usedDays,
            'remaining' => $remaining,
            'pending' => $pendingDays,
            'available' => max(0, $remaining - $pendingDays),
        ];
    }

    /**
     * Get all leave balances for an employee
     */
    public static function getAllBalances(int $employeeId, ?int $year = null): array
    {
        $year = $year ?? now()->year;
        $leaveTypes = LeaveType::where(function($query) {
            $query->whereNotNull('annual_entitlement')
                  ->orWhereNotNull('default_days');
        })->get();

        $balances = [];
        foreach ($leaveTypes as $leaveType) {
            $balances[$leaveType->code] = array_merge(
                ['leave_type' => $leaveType],
                static::getRemainingBalance($employeeId, $leaveType->id, $year)
            );
        }

        return $balances;
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($leaveRequest) {
            // Calculate working days
            if ($leaveRequest->start_date && $leaveRequest->end_date) {
                $leaveRequest->days_requested = $leaveRequest->calculateWorkingDays();
            }

            // Calculate financial impact if not set
            if (!$leaveRequest->daily_rate && $leaveRequest->employee) {
                $leaveRequest->daily_rate = $leaveRequest->calculateDailyRate();
            }

            // Set is_paid from leave type if not explicitly set
            if (is_null($leaveRequest->is_paid) && $leaveRequest->leaveType) {
                $leaveRequest->is_paid = $leaveRequest->leaveType->paid ?? true;
            }

            // Calculate financial impact
            if ($leaveRequest->daily_rate && $leaveRequest->days_requested) {
                $leaveRequest->financial_impact = $leaveRequest->calculateFinancialImpact();
            }

            // Auto-generate financial notes if empty
            if (!$leaveRequest->financial_notes && $leaveRequest->financial_impact) {
                $leaveRequest->financial_notes = $leaveRequest->generateFinancialNotes();
            }

            // Set approval timestamp
            if ($leaveRequest->isDirty('status') && in_array($leaveRequest->status, ['approved', 'declined'])) {
                if (!$leaveRequest->approved_at) {
                    $leaveRequest->approved_at = now();
                }
                if (!$leaveRequest->approved_by && auth()->check()) {
                    $leaveRequest->approved_by = auth()->id();
                }
            }
        });
    }

    /**
     * Generate financial notes automatically
     */
    private function generateFinancialNotes(): string
    {
        if (!$this->financial_impact || !$this->daily_rate) {
            return '';
        }

        $daysText = $this->days_requested . ' day' . ($this->days_requested > 1 ? 's' : '');
        $rateText = 'UGX ' . number_format($this->daily_rate, 2) . '/day';
        $impactText = 'UGX ' . number_format($this->financial_impact, 2);

        if ($this->is_paid) {
            return "PAID LEAVE: {$daysText} @ {$rateText} = {$impactText}. No salary deduction. Deducted from annual entitlement.";
        }

        return "UNPAID LEAVE: {$daysText} @ {$rateText} = {$impactText} salary deduction. To be processed in payroll.";
    }
}
