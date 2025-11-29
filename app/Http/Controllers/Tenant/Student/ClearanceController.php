<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClearanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $overdueInvoices = $user->invoices()
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now()->startOfDay())
            ->with('feeStructure')
            ->get();

        if ($overdueInvoices->isEmpty()) {
            return redirect()->route('tenant.student.dashboard')->with('success', 'You are cleared!');
        }

        return view('tenant.student.clearance.index', compact('overdueInvoices'));
    }
}
