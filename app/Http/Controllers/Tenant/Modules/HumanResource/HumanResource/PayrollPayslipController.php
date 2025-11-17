<?php

namespace App\Http\Controllers\Tenant\Modules\HumanResource;

use App\Http\Controllers\Controller;
use App\Models\PayrollRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\PDF; // from barryvdh/laravel-dompdf
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayrollPayslipController extends Controller
{
    /**
     * Display/download a payslip for a payroll record.
     */
    public function show(PayrollRecord $payroll)
    {
        $this->authorize('view', $payroll->employee); // Ensure user can view employee

        $payroll->load(['employee.department', 'employee.position']);
        $employee = $payroll->employee;

        $data = [
            'payroll' => $payroll,
            'employee' => $employee,
            'organization' => [
                'name' => config('app.name', 'School'),
                'address' => 'Jinja, Uganda',
                'phone' => '+256 000 000000',
                'email' => 'info@example.com',
            ],
        ];

        // If PDF facade available generate PDF, else fallback to HTML view
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenant.modules.human_resource.payroll.payslip', $data);
            $fileName = 'payslip_'.$payroll->payroll_number.'.pdf';
            return $pdf->download($fileName);
        }

        return view('tenant.modules.human_resource.payroll.payslip', $data);
    }
}
