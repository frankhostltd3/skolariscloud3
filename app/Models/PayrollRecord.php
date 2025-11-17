<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'payroll_number',
        'period_month',
        'period_year',
        'payment_date',
        'basic_salary',
        'allowances',
        'bonuses',
        'overtime_pay',
        'gross_salary',
        'tax_deduction',
        'nssf_deduction',
        'health_insurance',
        'loan_deduction',
        'other_deductions',
        'total_deductions',
        'net_salary',
        'payment_method',
        'payment_reference',
        'status',
        'working_days',
        'days_worked',
        'overtime_hours',
        'notes',
        'metadata',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'tax_deduction' => 'decimal:2',
        'nssf_deduction' => 'decimal:2',
        'health_insurance' => 'decimal:2',
        'loan_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'metadata' => 'array',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Scopes
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForPeriod($query, $month, $year)
    {
        return $query->where('period_month', $month)
                     ->where('period_year', $year);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Helper methods
     */
    public function getPeriodLabelAttribute(): string
    {
        $monthName = date('F', mktime(0, 0, 0, (int)$this->period_month, 1));
        return $monthName . ' ' . $this->period_year;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'paid' => 'bg-success',
            'approved' => 'bg-info',
            'pending' => 'bg-warning',
            'draft' => 'bg-secondary',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'bank_transfer' => 'Bank Transfer',
            'mobile_money' => 'Mobile Money',
            'cash' => 'Cash',
            'cheque' => 'Cheque',
            default => ucfirst($this->payment_method),
        };
    }

    /**
     * Calculate totals automatically before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($payroll) {
            // Calculate gross salary
            $payroll->gross_salary = $payroll->basic_salary + 
                                     $payroll->allowances + 
                                     $payroll->bonuses + 
                                     $payroll->overtime_pay;

            // Calculate total deductions
            $payroll->total_deductions = $payroll->tax_deduction + 
                                         $payroll->nssf_deduction + 
                                         $payroll->health_insurance + 
                                         $payroll->loan_deduction + 
                                         $payroll->other_deductions;

            // Calculate net salary
            $payroll->net_salary = $payroll->gross_salary - $payroll->total_deductions;
        });
    }
}
