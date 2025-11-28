<?php

namespace App\Http\Controllers\Tenant\Modules\HumanResource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PayrollRecordController extends Controller
{
    public function index()
    {
        // For now, we'll just return a simple view.
        // We can add logic to fetch payroll records later.
        return view('tenant.modules.human_resource.payroll.records');
    }
}
