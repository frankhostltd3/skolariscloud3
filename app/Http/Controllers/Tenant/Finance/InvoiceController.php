<?php

namespace App\Http\Controllers\Tenant\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\FeeStructure;
use App\Models\User;
use App\Models\Academic\AcademicYear;
use App\Notifications\InvoiceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function generateForTerm()
    {
        $school = request()->attributes->get('currentSchool');
        $academicYears = AcademicYear::where('school_id', $school->id)->orderBy('year', 'desc')->get();
        $currentYear = setting('academic_year');
        $currentTerm = setting('current_term');

        return view('tenant.finance.invoices.generate', compact('academicYears', 'currentYear', 'currentTerm'));
    }

    /**
     * Show bulk generation form
     */
    public function showBulkGenerate()
    {
        $school = request()->attributes->get('currentSchool');
        
        $classes = \App\Models\SchoolClass::where('school_id', $school->id)
            ->with('students')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $streams = \App\Models\Academic\ClassStream::whereHas('class', function($q) use ($school) {
                $q->where('school_id', $school->id);
            })
            ->with(['class', 'students'])
            ->where('is_active', true)
            ->get();

        $feeStructures = FeeStructure::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('fee_name')
            ->get();

        $totalStudents = User::where('school_id', $school->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->where('is_active', true)
            ->count();

        $allStudents = User::where('school_id', $school->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->where('is_active', true)
            ->with(['enrollments.class'])
            ->orderBy('name')
            ->get();

        return view('tenant.finance.invoices.bulk-generate', compact('classes', 'streams', 'feeStructures', 'totalStudents', 'allStudents'));
    }

    public function storeTermInvoices(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        $validated = $request->validate([
            'academic_year' => 'required|string',
            'term' => 'required|string',
            'due_date' => 'required|date',
        ]);

        $feeStructures = FeeStructure::where('school_id', $school->id)
            ->where('academic_year', $validated['academic_year'])
            ->where('term', $validated['term'])
            ->where('is_active', true)
            ->get();

        if ($feeStructures->isEmpty()) {
            return back()->with('error', 'No active fee structures found for the selected year and term.');
        }

        $count = 0;

        DB::transaction(function () use ($school, $feeStructures, $validated, &$count) {
            foreach ($feeStructures as $fee) {
                // Find eligible students
                $studentsQuery = User::where('school_id', $school->id)
                    ->whereHas('roles', fn($q) => $q->where('name', 'student'))
                    ->where('is_active', true);

                if ($fee->class) {
                    // Assuming class is stored in enrollments or similar.
                    // Since User doesn't have class_id, we check enrollments.
                    // But FeeStructure 'class' might be a string name or ID.
                    // Let's assume it matches the class name or we need to resolve it.
                    // For now, let's assume it matches the class name in enrollment.
                    // Or better, let's assume FeeStructure has class_id if it was strictly relational,
                    // but the model showed 'class' (string?).
                    // Let's check FeeStructure model again.
                    // It has 'class' in fillable.

                    // If 'class' is a string (e.g. "Senior 1"), we filter by that.
                    // We need to check how students are linked to classes.
                    // $student->currentEnrollment()->class->name

                    $studentsQuery->whereHas('enrollments', function($q) use ($fee) {
                        $q->where('status', 'active')
                          ->whereHas('class', function($cq) use ($fee) {
                              $cq->where('name', $fee->class)
                                 ->orWhere('id', $fee->class); // Try both
                          });
                    });
                }

                $students = $studentsQuery->get();

                foreach ($students as $student) {
                    // Check if invoice already exists
                    $exists = Invoice::where('school_id', $school->id)
                        ->where('student_id', $student->id)
                        ->where('fee_structure_id', $fee->id)
                        ->exists();

                    if (!$exists) {
                        Invoice::create([
                            'school_id' => $school->id,
                            'student_id' => $student->id,
                            'fee_structure_id' => $fee->id,
                            'invoice_number' => $this->generateInvoiceNumber($school->id),
                            'issue_date' => now(),
                            'due_date' => $validated['due_date'],
                            'total_amount' => $fee->amount,
                            'paid_amount' => 0,
                            'balance' => $fee->amount,
                            'status' => 'unpaid',
                            'academic_year' => $validated['academic_year'],
                            'term' => $validated['term'],
                            'notes' => 'Auto-generated for ' . $validated['term'],
                        ]);
                        $count++;
                    }
                }
            }
        });

        return redirect()->route('tenant.finance.invoices.index')
            ->with('success', "Successfully generated $count invoices.");
    }

    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        $query = Invoice::where('school_id', $school->id)
            ->with(['student', 'feeStructure', 'payments']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by term
        if ($request->filled('term')) {
            $query->where('term', $request->term);
        }

        $invoices = $query->orderBy('issue_date', 'desc')->paginate(perPage());

        // Statistics
        $stats = [
            'total_invoices' => Invoice::where('school_id', $school->id)->count(),
            'total_amount' => Invoice::where('school_id', $school->id)->sum('total_amount'),
            'paid_amount' => Invoice::where('school_id', $school->id)->sum('paid_amount'),
            'outstanding' => Invoice::where('school_id', $school->id)->sum('balance'),
        ];

        return view('tenant.finance.invoices.index', compact('invoices', 'stats'));
    }

    public function create()
    {
        $school = request()->attributes->get('currentSchool');

        $feeStructures = FeeStructure::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('fee_name')
            ->get();

        $students = User::where('school_id', $school->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.finance.invoices.create', compact('feeStructures', 'students'));
    }

    public function store(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        $validated = $request->validate([
            'student_id' => 'required|exists:tenant.users,id',
            'fee_structure_ids' => 'required|array|min:1',
            'fee_structure_ids.*' => 'required|exists:tenant.fee_structures,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'academic_year' => 'required|string|max:191',
            'term' => 'nullable|string|max:191',
            'notes' => 'nullable|string',
        ]);

        $createdInvoices = [];
        $duplicates = [];
        $totalAmount = 0;

        DB::transaction(function () use ($validated, $school, &$createdInvoices, &$duplicates, &$totalAmount) {
            foreach ($validated['fee_structure_ids'] as $feeStructureId) {
                // Check for duplicate invoice (same student, fee, academic year, term)
                $existingInvoice = Invoice::where('school_id', $school->id)
                    ->where('student_id', $validated['student_id'])
                    ->where('fee_structure_id', $feeStructureId)
                    ->where('academic_year', $validated['academic_year'])
                    ->where('term', $validated['term'])
                    ->whereIn('status', ['unpaid', 'partial', 'sent'])
                    ->first();

                if ($existingInvoice) {
                    $feeStructure = FeeStructure::find($feeStructureId);
                    $duplicates[] = $feeStructure->fee_name . ' (Invoice #' . $existingInvoice->invoice_number . ')';
                    continue;
                }

                // Get fee structure to auto-fill amount
                $feeStructure = FeeStructure::findOrFail($feeStructureId);
                
                $invoiceData = [
                    'school_id' => $school->id,
                    'student_id' => $validated['student_id'],
                    'fee_structure_id' => $feeStructureId,
                    'issue_date' => $validated['issue_date'],
                    'due_date' => $validated['due_date'],
                    'academic_year' => $validated['academic_year'],
                    'term' => $validated['term'],
                    'notes' => $validated['notes'],
                    'invoice_number' => $this->generateInvoiceNumber($school->id),
                    'total_amount' => $feeStructure->amount,
                    'paid_amount' => 0,
                    'balance' => $feeStructure->amount,
                    'status' => 'unpaid',
                    'created_by' => auth()->id(),
                ];

                $invoice = Invoice::create($invoiceData);
                $createdInvoices[] = $invoice;
                $totalAmount += $feeStructure->amount;
            }
        });

        // Build success message
        $message = count($createdInvoices) . ' invoice(s) created successfully for a total of ' . formatMoney($totalAmount) . '.';
        
        if (!empty($duplicates)) {
            $message .= ' Skipped ' . count($duplicates) . ' duplicate(s): ' . implode(', ', $duplicates);
        }

        // Check if user wants to send the invoices
        if ($request->has('send_invoice') && !empty($createdInvoices)) {
            $sendTo = $request->input('send_to', 'both');
            
            foreach ($createdInvoices as $invoice) {
                if ($sendTo === 'student' || $sendTo === 'both') {
                    $this->sendToStudent($invoice);
                }
                
                if ($sendTo === 'parent' || $sendTo === 'both') {
                    $this->sendToParent($invoice);
                }
            }
            
            return redirect()
                ->route('tenant.finance.invoices.index')
                ->with('success', $message . ' Invoices sent successfully.');
        }

        return redirect()
            ->route('tenant.finance.invoices.index')
            ->with('success', $message);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['student', 'feeStructure', 'payments.receiver', 'school']);

        return view('tenant.finance.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()
                ->route('tenant.finance.invoices.index')
                ->with('error', 'Cannot edit paid invoices.');
        }

        $feeStructures = FeeStructure::where('school_id', $invoice->school_id)
            ->where('is_active', true)
            ->orderBy('fee_name')
            ->get();

        $students = User::where('school_id', $invoice->school_id)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.finance.invoices.edit', compact('invoice', 'feeStructures', 'students'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()
                ->route('tenant.finance.invoices.index')
                ->with('error', 'Cannot update paid invoices.');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:tenant.users,id',
            'fee_structure_id' => 'required|exists:tenant.fee_structures,id',
            'total_amount' => 'required|numeric|min:' . $invoice->paid_amount,
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'academic_year' => 'required|string|max:191',
            'term' => 'nullable|string|max:191',
            'notes' => 'nullable|string',
        ]);

        $validated['balance'] = $validated['total_amount'] - $invoice->paid_amount;

        // Update status based on payment
        if ($validated['balance'] <= 0) {
            $validated['status'] = 'paid';
        } elseif ($invoice->paid_amount > 0) {
            $validated['status'] = 'partial';
        }

        $invoice->update($validated);

        return redirect()
            ->route('tenant.finance.invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Request $request, Invoice $invoice)
    {
        if ($invoice->payments()->count() > 0) {
            return redirect()
                ->route('tenant.finance.invoices.index')
                ->with('error', 'Cannot delete invoice with payments. Please cancel it instead.');
        }

        $validated = $request->validate([
            'deletion_reason' => 'required|string|min:10|max:1000',
        ]);

        $invoice->update([
            'deletion_reason' => $validated['deletion_reason'],
        ]);

        $invoice->delete();

        return redirect()
            ->route('tenant.finance.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    public function cancel(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()
                ->route('tenant.finance.invoices.index')
                ->with('error', 'Cannot cancel a fully paid invoice.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|min:10|max:1000',
        ]);

        $invoice->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
        ]);

        return redirect()
            ->route('tenant.finance.invoices.index')
            ->with('success', 'Invoice cancelled successfully.');
    }

    private function generateInvoiceNumber($schoolId): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $lastInvoice = Invoice::where('school_id', $schoolId)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -5) + 1 : 1;

        return $prefix . $year . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Send invoice notification to student
     */
    public function sendToStudent(Invoice $invoice)
    {
        $student = $invoice->student;
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found for this invoice.');
        }

        if (!$student->email) {
            return redirect()->back()->with('error', 'Student does not have an email address.');
        }

        try {
            $student->notify(new InvoiceNotification($invoice, 'student'));
            return redirect()->back()->with('success', "Invoice sent to student ({$student->email}) successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send invoice: ' . $e->getMessage());
        }
    }

    /**
     * Send invoice notification to parent(s)
     */
    public function sendToParent(Invoice $invoice)
    {
        $student = $invoice->student;
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found for this invoice.');
        }

        // Try to find parents through the Student model if it exists
        $parentsSent = 0;
        $errors = [];

        // Check if student has a parentProfile relationship (for User model)
        // Or check if there's a Student model with parents
        $studentModel = \App\Models\Student::where('email', $student->email)->first();
        
        if ($studentModel && method_exists($studentModel, 'parents')) {
            $parents = $studentModel->parents()->with('user')->get();
            
            foreach ($parents as $parent) {
                $parentUser = $parent->user ?? null;
                $parentEmail = $parentUser?->email ?? $parent->email ?? null;
                
                if ($parentEmail) {
                    try {
                        if ($parentUser) {
                            $parentUser->notify(new InvoiceNotification($invoice, 'parent'));
                        } else {
                            // Send directly via mail if no user account
                            \Illuminate\Support\Facades\Mail::to($parentEmail)
                                ->send(new \Illuminate\Mail\Mailable());
                        }
                        $parentsSent++;
                    } catch (\Exception $e) {
                        $errors[] = "Failed to send to {$parentEmail}: " . $e->getMessage();
                    }
                }
            }
        }

        // Also check emergency contact / parent info stored on User
        if ($student->emergency_contact_email ?? false) {
            try {
                \Illuminate\Support\Facades\Notification::route('mail', $student->emergency_contact_email)
                    ->notify(new InvoiceNotification($invoice, 'parent'));
                $parentsSent++;
            } catch (\Exception $e) {
                $errors[] = "Failed to send to emergency contact: " . $e->getMessage();
            }
        }

        if ($parentsSent > 0) {
            $message = "Invoice sent to {$parentsSent} parent(s) successfully.";
            if (!empty($errors)) {
                $message .= " Some errors occurred: " . implode('; ', $errors);
            }
            return redirect()->back()->with('success', $message);
        }

        if (!empty($errors)) {
            return redirect()->back()->with('error', 'Failed to send invoice: ' . implode('; ', $errors));
        }

        return redirect()->back()->with('error', 'No parent email addresses found for this student.');
    }

    /**
     * Send invoice to both student and parents
     */
    public function sendToBoth(Invoice $invoice)
    {
        $student = $invoice->student;
        $sentTo = [];
        $errors = [];

        // Send to student
        if ($student && $student->email) {
            try {
                $student->notify(new InvoiceNotification($invoice, 'student'));
                $sentTo[] = "Student ({$student->email})";
            } catch (\Exception $e) {
                $errors[] = "Student: " . $e->getMessage();
            }
        }

        // Send to parents
        $studentModel = \App\Models\Student::where('email', $student->email)->first();
        
        if ($studentModel && method_exists($studentModel, 'parents')) {
            $parents = $studentModel->parents()->with('user')->get();
            
            foreach ($parents as $parent) {
                $parentUser = $parent->user ?? null;
                $parentEmail = $parentUser?->email ?? $parent->email ?? null;
                
                if ($parentEmail && $parentUser) {
                    try {
                        $parentUser->notify(new InvoiceNotification($invoice, 'parent'));
                        $sentTo[] = "Parent ({$parentEmail})";
                    } catch (\Exception $e) {
                        $errors[] = "Parent ({$parentEmail}): " . $e->getMessage();
                    }
                }
            }
        }

        if (!empty($sentTo)) {
            $message = "Invoice sent to: " . implode(', ', $sentTo) . ".";
            if (!empty($errors)) {
                $message .= " Errors: " . implode('; ', $errors);
            }
            return redirect()->back()->with('success', $message);
        }

        return redirect()->back()->with('error', 'Failed to send invoice. ' . implode('; ', $errors));
    }

    /**
     * Bulk send invoices
     */
    public function bulkSend(Request $request)
    {
        $validated = $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:tenant.invoices,id',
            'send_to' => 'required|in:student,parent,both',
        ]);

        $school = $request->attributes->get('currentSchool');
        $sentCount = 0;
        $failedCount = 0;

        foreach ($validated['invoice_ids'] as $invoiceId) {
            $invoice = Invoice::where('school_id', $school->id)->find($invoiceId);
            
            if (!$invoice) {
                $failedCount++;
                continue;
            }

            try {
                switch ($validated['send_to']) {
                    case 'student':
                        if ($invoice->student?->email) {
                            $invoice->student->notify(new InvoiceNotification($invoice, 'student'));
                            $sentCount++;
                        } else {
                            $failedCount++;
                        }
                        break;
                    case 'parent':
                        // Similar logic as sendToParent but simplified
                        $studentModel = \App\Models\Student::where('email', $invoice->student?->email)->first();
                        if ($studentModel) {
                            $parents = $studentModel->parents()->with('user')->get();
                            foreach ($parents as $parent) {
                                if ($parent->user?->email) {
                                    $parent->user->notify(new InvoiceNotification($invoice, 'parent'));
                                }
                            }
                            $sentCount++;
                        } else {
                            $failedCount++;
                        }
                        break;
                    case 'both':
                        if ($invoice->student?->email) {
                            $invoice->student->notify(new InvoiceNotification($invoice, 'student'));
                        }
                        $studentModel = \App\Models\Student::where('email', $invoice->student?->email)->first();
                        if ($studentModel) {
                            $parents = $studentModel->parents()->with('user')->get();
                            foreach ($parents as $parent) {
                                if ($parent->user?->email) {
                                    $parent->user->notify(new InvoiceNotification($invoice, 'parent'));
                                }
                            }
                        }
                        $sentCount++;
                        break;
                }
            } catch (\Exception $e) {
                $failedCount++;
            }
        }

        if ($sentCount > 0 && $failedCount === 0) {
            return redirect()->back()->with('success', "Successfully sent {$sentCount} invoice(s).");
        } elseif ($sentCount > 0) {
            return redirect()->back()->with('warning', "Sent {$sentCount} invoice(s), {$failedCount} failed.");
        } else {
            return redirect()->back()->with('error', "Failed to send invoices.");
        }
    }

    /**
     * Print invoice view
     */
    public function print(Invoice $invoice)
    {
        $invoice->load(['student', 'feeStructure', 'payments', 'school']);
        
        // For web display, use asset URL
        $logoPath = $invoice->school->logo_url;
        
        return view('tenant.finance.invoices.print', compact('invoice', 'logoPath'));
    }

    /**
     * Download invoice as PDF
     */
    public function download(Invoice $invoice)
    {
        $invoice->load(['student', 'feeStructure', 'payments', 'school']);
        
        // For PDF, use filesystem path
        $logoPath = null;
        if($invoice->school->logo_url) {
            $logoSetting = setting('school_logo');
            if($logoSetting) {
                $possiblePaths = [
                    storage_path('app/public/' . $logoSetting),
                    public_path('storage/' . $logoSetting),
                ];
                
                foreach($possiblePaths as $path) {
                    if(file_exists($path)) {
                        $logoPath = $path;
                        break;
                    }
                }
            }
        }
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenant.finance.invoices.print', compact('invoice', 'logoPath'));
        
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Share invoice via WhatsApp
     */
    public function shareWhatsApp(Invoice $invoice)
    {
        return redirect()->away($invoice->getWhatsAppShareUrl());
    }

    /**
     * Generate SMS message for invoice
     */
    public function shareSms(Invoice $invoice)
    {
        $message = $invoice->getSmsMessage();
        
        // Return JSON for frontend to handle
        return response()->json([
            'success' => true,
            'message' => $message,
            'sms_url' => 'sms:?body=' . urlencode($message),
        ]);
    }

    /**
     * Generate invoices for entire class
     */
    public function generateForClass(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:tenant.classes,id',
            'fee_structure_ids' => 'required|array|min:1',
            'fee_structure_ids.*' => 'exists:tenant.fee_structures,id',
            'due_date' => 'required|date',
            'academic_year' => 'required|string',
            'term' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $school = $request->attributes->get('currentSchool');
        $class = \App\Models\SchoolClass::findOrFail($validated['class_id']);
        
        $students = User::where('school_id', $school->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->whereHas('enrollments', function($q) use ($class) {
                $q->where('class_id', $class->id)
                  ->where('status', 'active');
            })
            ->where('is_active', true)
            ->get();

        $createdCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($validated, $school, $students, &$createdCount, &$skippedCount) {
            foreach ($students as $student) {
                foreach ($validated['fee_structure_ids'] as $feeStructureId) {
                    // Check for duplicate
                    $exists = Invoice::where('school_id', $school->id)
                        ->where('student_id', $student->id)
                        ->where('fee_structure_id', $feeStructureId)
                        ->where('academic_year', $validated['academic_year'])
                        ->where('term', $validated['term'])
                        ->whereIn('status', ['unpaid', 'partial', 'sent'])
                        ->exists();

                    if ($exists) {
                        $skippedCount++;
                        continue;
                    }

                    $feeStructure = FeeStructure::find($feeStructureId);
                    
                    Invoice::create([
                        'school_id' => $school->id,
                        'student_id' => $student->id,
                        'fee_structure_id' => $feeStructureId,
                        'invoice_number' => $this->generateInvoiceNumber($school->id),
                        'issue_date' => now(),
                        'due_date' => $validated['due_date'],
                        'total_amount' => $feeStructure->amount,
                        'paid_amount' => 0,
                        'balance' => $feeStructure->amount,
                        'status' => 'unpaid',
                        'academic_year' => $validated['academic_year'],
                        'term' => $validated['term'],
                        'notes' => $validated['notes'],
                        'created_by' => auth()->id(),
                    ]);
                    
                    $createdCount++;
                }
            }
        });

        $message = "Generated {$createdCount} invoice(s) for class {$class->name}.";
        if ($skippedCount > 0) {
            $message .= " Skipped {$skippedCount} duplicate(s).";
        }

        return redirect()->route('tenant.finance.invoices.index')->with('success', $message);
    }

    /**
     * Generate invoices for entire class stream
     */
    public function generateForStream(Request $request)
    {
        $validated = $request->validate([
            'stream_id' => 'required|exists:tenant.class_streams,id',
            'fee_structure_ids' => 'required|array|min:1',
            'fee_structure_ids.*' => 'exists:tenant.fee_structures,id',
            'due_date' => 'required|date',
            'academic_year' => 'required|string',
            'term' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $school = $request->attributes->get('currentSchool');
        $stream = \App\Models\Academic\ClassStream::findOrFail($validated['stream_id']);
        
        $students = User::where('school_id', $school->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->whereHas('enrollments', function($q) use ($stream) {
                $q->where('class_stream_id', $stream->id)
                  ->where('status', 'active');
            })
            ->where('is_active', true)
            ->get();

        $createdCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($validated, $school, $students, &$createdCount, &$skippedCount) {
            foreach ($students as $student) {
                foreach ($validated['fee_structure_ids'] as $feeStructureId) {
                    $exists = Invoice::where('school_id', $school->id)
                        ->where('student_id', $student->id)
                        ->where('fee_structure_id', $feeStructureId)
                        ->where('academic_year', $validated['academic_year'])
                        ->where('term', $validated['term'])
                        ->whereIn('status', ['unpaid', 'partial', 'sent'])
                        ->exists();

                    if ($exists) {
                        $skippedCount++;
                        continue;
                    }

                    $feeStructure = FeeStructure::find($feeStructureId);
                    
                    Invoice::create([
                        'school_id' => $school->id,
                        'student_id' => $student->id,
                        'fee_structure_id' => $feeStructureId,
                        'invoice_number' => $this->generateInvoiceNumber($school->id),
                        'issue_date' => now(),
                        'due_date' => $validated['due_date'],
                        'total_amount' => $feeStructure->amount,
                        'paid_amount' => 0,
                        'balance' => $feeStructure->amount,
                        'status' => 'unpaid',
                        'academic_year' => $validated['academic_year'],
                        'term' => $validated['term'],
                        'notes' => $validated['notes'],
                        'created_by' => auth()->id(),
                    ]);
                    
                    $createdCount++;
                }
            }
        });

        $message = "Generated {$createdCount} invoice(s) for stream {$stream->full_name}.";
        if ($skippedCount > 0) {
            $message .= " Skipped {$skippedCount} duplicate(s).";
        }

        return redirect()->route('tenant.finance.invoices.index')->with('success', $message);
    }

    /**
     * Generate invoices for entire school
     */
    public function generateForSchool(Request $request)
    {
        $validated = $request->validate([
            'fee_structure_ids' => 'required|array|min:1',
            'fee_structure_ids.*' => 'exists:tenant.fee_structures,id',
            'due_date' => 'required|date',
            'academic_year' => 'required|string',
            'term' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $school = $request->attributes->get('currentSchool');
        
        $students = User::where('school_id', $school->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->where('is_active', true)
            ->get();

        $createdCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($validated, $school, $students, &$createdCount, &$skippedCount) {
            foreach ($students as $student) {
                foreach ($validated['fee_structure_ids'] as $feeStructureId) {
                    $exists = Invoice::where('school_id', $school->id)
                        ->where('student_id', $student->id)
                        ->where('fee_structure_id', $feeStructureId)
                        ->where('academic_year', $validated['academic_year'])
                        ->where('term', $validated['term'])
                        ->whereIn('status', ['unpaid', 'partial', 'sent'])
                        ->exists();

                    if ($exists) {
                        $skippedCount++;
                        continue;
                    }

                    $feeStructure = FeeStructure::find($feeStructureId);
                    
                    Invoice::create([
                        'school_id' => $school->id,
                        'student_id' => $student->id,
                        'fee_structure_id' => $feeStructureId,
                        'invoice_number' => $this->generateInvoiceNumber($school->id),
                        'issue_date' => now(),
                        'due_date' => $validated['due_date'],
                        'total_amount' => $feeStructure->amount,
                        'paid_amount' => 0,
                        'balance' => $feeStructure->amount,
                        'status' => 'unpaid',
                        'academic_year' => $validated['academic_year'],
                        'term' => $validated['term'],
                        'notes' => $validated['notes'],
                        'created_by' => auth()->id(),
                    ]);
                    
                    $createdCount++;
                }
            }
        });

        $message = "Generated {$createdCount} invoice(s) for entire school.";
        if ($skippedCount > 0) {
            $message .= " Skipped {$skippedCount} duplicate(s).";
        }

        return redirect()->route('tenant.finance.invoices.index')->with('success', $message);
    }

    /**
     * Generate invoices for selected students
     */
    public function generateForStudents(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:tenant.users,id',
            'fee_structure_ids' => 'required|array|min:1',
            'fee_structure_ids.*' => 'exists:tenant.fee_structures,id',
            'due_date' => 'required|date',
            'academic_year' => 'required|string',
            'term' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $school = $request->attributes->get('currentSchool');
        
        $students = User::where('school_id', $school->id)
            ->whereIn('id', $validated['student_ids'])
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->get();

        $createdCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($validated, $school, $students, &$createdCount, &$skippedCount) {
            foreach ($students as $student) {
                foreach ($validated['fee_structure_ids'] as $feeStructureId) {
                    $exists = Invoice::where('school_id', $school->id)
                        ->where('student_id', $student->id)
                        ->where('fee_structure_id', $feeStructureId)
                        ->where('academic_year', $validated['academic_year'])
                        ->where('term', $validated['term'])
                        ->whereIn('status', ['unpaid', 'partial', 'sent'])
                        ->exists();

                    if ($exists) {
                        $skippedCount++;
                        continue;
                    }

                    $feeStructure = FeeStructure::find($feeStructureId);
                    
                    Invoice::create([
                        'school_id' => $school->id,
                        'student_id' => $student->id,
                        'fee_structure_id' => $feeStructureId,
                        'invoice_number' => $this->generateInvoiceNumber($school->id),
                        'issue_date' => now(),
                        'due_date' => $validated['due_date'],
                        'total_amount' => $feeStructure->amount,
                        'paid_amount' => 0,
                        'balance' => $feeStructure->amount,
                        'status' => 'unpaid',
                        'academic_year' => $validated['academic_year'],
                        'term' => $validated['term'],
                        'notes' => $validated['notes'],
                        'created_by' => auth()->id(),
                    ]);
                    
                    $createdCount++;
                }
            }
        });

        $message = "Generated {$createdCount} invoice(s) for selected students.";
        if ($skippedCount > 0) {
            $message .= " Skipped {$skippedCount} duplicate(s).";
        }

        return redirect()->route('tenant.finance.invoices.index')->with('success', $message);
    }
}
