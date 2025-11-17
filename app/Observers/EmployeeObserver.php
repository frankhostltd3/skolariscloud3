<?php

namespace App\Observers;

use App\Models\Employee;
use Illuminate\Support\Str;

class EmployeeObserver
{
    /**
     * Handle the Employee "creating" event.
     */
    public function creating(Employee $employee): void
    {
        // Auto-generate employee number if not provided
        if (empty($employee->employee_number)) {
            $employee->employee_number = $this->generateEmployeeNumber();
        }
    }

    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "restored" event.
     */
    public function restored(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "force deleted" event.
     */
    public function forceDeleted(Employee $employee): void
    {
        //
    }

    /**
     * Generate a unique employee number.
     */
    private function generateEmployeeNumber(): string
    {
        do {
            $number = 'EMP' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Employee::where('employee_number', $number)->exists());

        return $number;
    }
}
