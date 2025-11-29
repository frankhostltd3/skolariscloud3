<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class FeesController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Assuming user is a student
        $invoices = $user->invoices()->with(['feeStructure', 'payments'])->orderBy('created_at', 'desc')->get();

        $totalDue = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('paid_amount');
        $balance = $invoices->sum('balance');

        return view('tenant.student.fees.index', compact('invoices', 'totalDue', 'totalPaid', 'balance'));
    }

    public function show(Invoice $invoice)
    {
        $user = Auth::user();

        if ($invoice->student_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $invoice->load(['feeStructure', 'payments']);

        return view('tenant.student.fees.show', compact('invoice'));
    }

    public function pay(Invoice $invoice)
    {
        // Placeholder for payment integration
        // In a real app, this would redirect to a payment gateway
        // For now, we'll just show a "Pay" page or redirect back with a message

        return redirect()->route('tenant.student.fees.show', $invoice)
            ->with('info', 'Online payment integration coming soon. Please use bank transfer.');
    }
}
