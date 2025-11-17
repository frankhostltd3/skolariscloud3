<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\FeeAssignment;
use App\Models\PaymentTransaction;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FeesController extends Controller
{
    public function index(Request $request): View
    {
        $query = Fee::query()->with(['assignments.assignedClass', 'assignments.assignedStudent']);

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('recurring_type')) {
            $query->where('recurring_type', $request->recurring_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('due_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('due_date', '<=', $request->date_to);
        }

        $fees = $query->latest()->paginate(15)->withQueryString();

        // Calculate statistics
        $totalFees = Fee::where('is_active', true)->sum('amount');
        
        // Check if payment_transactions table exists
        try {
            $paidAmount = PaymentTransaction::where('transaction_type', 'fee_payment')
                ->where('status', 'completed')
                ->sum('amount');
            
            // Recent payments
            $recentPayments = PaymentTransaction::where('transaction_type', 'fee_payment')
                ->where('status', 'completed')
                ->latest()
                ->take(10)
                ->get();
        } catch (\Exception $e) {
            // Table doesn't exist yet
            $paidAmount = 0;
            $recentPayments = collect([]);
        }
        
        $outstandingAmount = $totalFees - $paidAmount;
        
        $overdueFees = Fee::where('is_active', true)
            ->where('due_date', '<', now())
            ->count();

        // Get categories for filter
        $categories = Fee::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        return view('tenant.modules.fees.index', compact(
            'fees',
            'totalFees',
            'paidAmount',
            'outstandingAmount',
            'overdueFees',
            'categories',
            'recentPayments'
        ));
    }

    public function create(): View
    {
        $classes = SchoolClass::active()->orderBy('name')->get();
        $students = Student::orderBy('first_name')->get();
        
        return view('tenant.modules.fees.create', compact('classes', 'students'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'due_date' => 'required|date',
            'recurring_type' => 'required|in:one-time,monthly,quarterly,yearly,term-based',
            'applicable_to' => 'required|in:all,specific_class,specific_student',
            'class_id' => 'nullable|exists:school_classes,id',
            'student_id' => 'nullable|exists:students,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $fee = Fee::create($validated);

        // Auto-create assignments based on applicable_to
        if ($validated['applicable_to'] === 'specific_class' && $validated['class_id']) {
            FeeAssignment::create([
                'fee_id' => $fee->id,
                'assignment_type' => 'class',
                'class_id' => $validated['class_id'],
                'effective_date' => now(),
                'is_active' => true,
            ]);
        } elseif ($validated['applicable_to'] === 'specific_student' && $validated['student_id']) {
            FeeAssignment::create([
                'fee_id' => $fee->id,
                'assignment_type' => 'student',
                'student_id' => $validated['student_id'],
                'effective_date' => now(),
                'is_active' => true,
            ]);
        }

        return redirect()->route('tenant.modules.fees.index')
            ->with('success', 'Fee created successfully.');
    }

    public function show(Fee $fee): View
    {
        $fee->load(['assignments.assignedClass', 'assignments.assignedStudent']);

        // Get payment history for this fee
        try {
            $payments = PaymentTransaction::where('transaction_type', 'fee_payment')
                ->where('related_id', $fee->id)
                ->latest()
                ->get();

            $totalCollected = $payments->where('status', 'completed')->sum('amount');
            $pendingPayments = $payments->where('status', 'pending')->count();
        } catch (\Exception $e) {
            // Table doesn't exist yet
            $payments = collect([]);
            $totalCollected = 0;
            $pendingPayments = 0;
        }

        // Get assigned students count
        $assignedStudentsCount = $fee->assignments()
            ->where('is_active', true)
            ->count();

        return view('tenant.modules.fees.show', compact(
            'fee',
            'payments',
            'totalCollected',
            'pendingPayments',
            'assignedStudentsCount'
        ));
    }

    public function edit(Fee $fee): View
    {
        $classes = SchoolClass::active()->orderBy('name')->get();
        $students = Student::orderBy('first_name')->get();
        
        return view('tenant.modules.fees.edit', compact('fee', 'classes', 'students'));
    }

    public function update(Request $request, Fee $fee): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'due_date' => 'required|date',
            'recurring_type' => 'required|in:one-time,monthly,quarterly,yearly,term-based',
            'applicable_to' => 'required|in:all,specific_class,specific_student',
            'class_id' => 'nullable|exists:school_classes,id',
            'student_id' => 'nullable|exists:students,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $fee->update($validated);

        return redirect()->route('tenant.modules.fees.show', $fee)
            ->with('success', 'Fee updated successfully.');
    }

    public function destroy(Fee $fee): RedirectResponse
    {
        // Check if fee has any payments
        try {
            $hasPayments = PaymentTransaction::where('transaction_type', 'fee_payment')
                ->where('related_id', $fee->id)
                ->where('status', 'completed')
                ->exists();

            if ($hasPayments) {
                return back()->with('error', 'Cannot delete fee with existing payments. Please deactivate instead.');
            }
        } catch (\Exception $e) {
            // Table doesn't exist, safe to delete
        }

        $fee->delete();

        return redirect()->route('tenant.modules.fees.index')
            ->with('success', 'Fee deleted successfully.');
    }

    public function assign(Fee $fee): View
    {
        $classes = SchoolClass::active()->orderBy('name')->get();
        $students = Student::orderBy('first_name')->get();
        $existingAssignments = $fee->assignments()->where('is_active', true)->get();

        return view('tenant.modules.fees.assign', compact('fee', 'classes', 'students', 'existingAssignments'));
    }

    public function storeAssignment(Request $request, Fee $fee): RedirectResponse
    {
        $validated = $request->validate([
            'assignment_type' => 'required|in:class,student,bulk_students',
            'class_id' => 'required_if:assignment_type,class|nullable|exists:school_classes,id',
            'student_id' => 'required_if:assignment_type,student|nullable|exists:students,id',
            'student_ids' => 'required_if:assignment_type,bulk_students|nullable|array',
            'student_ids.*' => 'exists:students,id',
            'effective_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validated['assignment_type'] === 'bulk_students' && isset($validated['student_ids'])) {
            // Bulk assign to multiple students
            foreach ($validated['student_ids'] as $studentId) {
                FeeAssignment::create([
                    'fee_id' => $fee->id,
                    'assignment_type' => 'student',
                    'student_id' => $studentId,
                    'effective_date' => $validated['effective_date'],
                    'notes' => $validated['notes'],
                    'is_active' => true,
                ]);
            }
            $message = 'Fee assigned to ' . count($validated['student_ids']) . ' students successfully.';
        } else {
            // Single assignment
            FeeAssignment::create([
                'fee_id' => $fee->id,
                'assignment_type' => $validated['assignment_type'],
                'class_id' => $validated['class_id'] ?? null,
                'student_id' => $validated['student_id'] ?? null,
                'effective_date' => $validated['effective_date'],
                'notes' => $validated['notes'],
                'is_active' => true,
            ]);
            $message = 'Fee assigned successfully.';
        }

        return redirect()->route('tenant.modules.fees.show', $fee)
            ->with('success', $message);
    }

    public function removeAssignment(FeeAssignment $assignment): RedirectResponse
    {
        $feeId = $assignment->fee_id;
        $assignment->delete();

        return redirect()->route('tenant.modules.fees.show', $feeId)
            ->with('success', 'Assignment removed successfully.');
    }

    public function recordPayment(Fee $fee): View
    {
        $students = Student::orderBy('first_name')->get();
        
        return view('tenant.modules.fees.record_payment', compact('fee', 'students'));
    }

    public function storePayment(Request $request, Fee $fee): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,online,mobile_money',
            'reference' => 'nullable|string|max:100',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $student = Student::findOrFail($validated['student_id']);

        PaymentTransaction::create([
            'transaction_type' => 'fee_payment',
            'related_id' => $fee->id,
            'gateway' => $validated['payment_method'],
            'transaction_id' => 'PAY-' . strtoupper(uniqid()),
            'reference' => $validated['reference'] ?? 'Manual Payment',
            'amount' => $validated['amount'],
            'currency' => 'USD', // TODO: Get from settings
            'status' => 'completed',
            'payer_name' => $student->full_name,
            'payer_email' => $student->email,
            'description' => "Payment for {$fee->name}",
            'notes' => $validated['notes'],
            'initiated_at' => $validated['payment_date'],
            'completed_at' => $validated['payment_date'],
        ]);

        return redirect()->route('tenant.modules.fees.show', $fee)
            ->with('success', 'Payment recorded successfully.');
    }
}
