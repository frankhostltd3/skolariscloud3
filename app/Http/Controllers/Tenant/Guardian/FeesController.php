<?php

namespace App\Http\Controllers\Tenant\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Finance\FeeInvoice;
use App\Models\Finance\FeePayment;
use App\Models\Student;
use App\Models\TenantPaymentGatewayConfig;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FeesController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile()->with([
            'students' => function ($query) {
                $query->with(['class', 'stream']);
            },
        ])->first();

        $wards = $parentProfile?->students ?? collect();
        $selectedWardId = (int) $request->input('student_id', 0);

        if ($wards->isEmpty()) {
            return view('tenant.parent.fees.index', [
                'parentProfile' => $parentProfile,
                'wards' => $wards,
                'selectedWard' => null,
                'selectedWardUser' => null,
                'wardSummaries' => collect(),
                'feeItems' => collect(),
                'paymentHistory' => collect(),
                'invoices' => collect(),
                'activeGateways' => TenantPaymentGatewayConfig::active()->ordered()->get(),
                'escrowHoldHours' => 24,
                'totals' => [
                    'assigned' => 0.0,
                    'paid' => 0.0,
                    'outstanding' => 0.0,
                    'overdue' => 0,
                    'upcoming' => 0,
                ],
            ]);
        }

        if ($selectedWardId !== 0 && !$wards->contains('id', $selectedWardId)) {
            abort(403, __('You are not authorized to view this student.'));
        }

        $selectedWard = $selectedWardId !== 0
            ? $wards->firstWhere('id', $selectedWardId)
            : $wards->first();

        $wardUsers = $this->resolveWardUsers($wards);
        $selectedWardUser = $selectedWard ? $wardUsers->get($selectedWard->id) : null;

        $wardSummaries = $wards->mapWithKeys(function (Student $ward) use ($wardUsers) {
            $wardUser = $wardUsers->get($ward->id);
            $fees = $this->loadFeesForStudent($ward, $wardUser);

            return [$ward->id => [
                'ward' => $ward,
                'total_assigned' => $fees->sum('amount'),
                'total_paid' => $fees->sum('paid'),
                'total_outstanding' => $fees->sum('balance'),
                'overdue' => $fees->where('is_overdue', true)->count(),
                'upcoming' => $fees->where('is_due_soon', true)->count(),
            ]];
        });

        $feeItems = $selectedWard ? $this->loadFeesForStudent($selectedWard, $selectedWardUser) : collect();

        $totals = [
            'assigned' => $feeItems->sum('amount'),
            'paid' => $feeItems->sum('paid'),
            'outstanding' => $feeItems->sum('balance'),
            'overdue' => $feeItems->where('is_overdue', true)->count(),
            'upcoming' => $feeItems->where('is_due_soon', true)->count(),
        ];

        $paymentHistory = $selectedWardUser
            ? FeePayment::with('invoice')
                ->where('student_id', $selectedWardUser->id)
                ->latest('paid_at')
                ->latest('created_at')
                ->take(20)
                ->get()
            : collect();

        $invoices = $selectedWardUser
            ? FeeInvoice::with('payments')
                ->where('student_id', $selectedWardUser->id)
                ->latest('due_date')
                ->latest('created_at')
                ->get()
            : collect();

        $activeGateways = TenantPaymentGatewayConfig::active()->ordered()->get();

        return view('tenant.parent.fees.index', [
            'parentProfile' => $parentProfile,
            'wards' => $wards,
            'selectedWard' => $selectedWard,
            'selectedWardUser' => $selectedWardUser,
            'wardSummaries' => $wardSummaries,
            'feeItems' => $feeItems,
            'paymentHistory' => $paymentHistory,
            'invoices' => $invoices,
            'activeGateways' => $activeGateways,
            'escrowHoldHours' => 24,
            'totals' => $totals,
        ]);
    }

    public function pay(Request $request, Fee $fee): RedirectResponse
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile()->with('students')->first();
        $wards = $parentProfile?->students ?? collect();

        $wardId = (int) $request->input('student_id');
        $ward = $wards->firstWhere('id', $wardId);

        if (!$ward) {
            return redirect()->route('tenant.parent.fees.index')->with('error', __('Selected student is not linked to your account.'));
        }

        $wardUsers = $this->resolveWardUsers($wards);
        $wardUser = $wardUsers->get($ward->id);

        if (!$wardUser) {
            return redirect()->route('tenant.parent.fees.index', ['student_id' => $ward->id])
                ->with('error', __('This student does not have an activated portal account yet. Please contact the school.'));
        }

        $feesForWard = $this->loadFeesForStudent($ward, $wardUser);
        $targetFee = $feesForWard->firstWhere('id', $fee->id);

        if (!$targetFee) {
            return redirect()->route('tenant.parent.fees.index', ['student_id' => $ward->id])
                ->with('error', __('This fee is not assigned to your child.'));
        }

        $outstanding = $targetFee['balance'];

        if ($outstanding <= 0) {
            return redirect()->route('tenant.parent.fees.index', ['student_id' => $ward->id])
                ->with('info', __('This fee is already fully settled.'));
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.5', 'max:' . number_format($outstanding, 2, '.', '')],
            'payment_method' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $activeGateways = TenantPaymentGatewayConfig::active()->ordered()->pluck('gateway');
        if ($activeGateways->isNotEmpty() && !$activeGateways->contains($validated['payment_method'])) {
            return redirect()->route('tenant.parent.fees.index', ['student_id' => $ward->id])
                ->with('error', __('Selected payment method is not enabled.'));
        }

        $holdUntil = now()->addHours(24);

        $invoice = FeeInvoice::firstOrCreate([
            'student_id' => $wardUser->id,
            'status' => 'pending',
        ], [
            'total_amount' => $targetFee['amount'],
            'currency' => currency_code(),
            'due_date' => $fee->due_date ?? now()->addDays(7),
            'notes' => __('Auto-generated invoice for :fee', ['fee' => $fee->name]),
            'created_by' => $user->id,
        ]);

        $payment = FeePayment::create([
            'fee_invoice_id' => $invoice->id,
            'student_id' => $wardUser->id,
            'amount' => (float) $validated['amount'],
            'currency' => currency_code(),
            'method' => $validated['payment_method'],
            'reference' => 'ESCROW-' . strtoupper(uniqid()),
            'paid_at' => now(),
            'status' => 'pending',
            'meta' => [
                'source' => 'parent-portal',
                'fee_id' => $fee->id,
                'ward_id' => $ward->id,
                'hold_until' => $holdUntil->toIso8601String(),
                'notes' => $validated['notes'] ?? null,
            ],
            'received_by' => null,
        ]);

        $invoice->refresh();
        $confirmedPaid = (float) $invoice->payments()->where('status', 'confirmed')->sum('amount');
        $balance = max(0.0, (float) $invoice->total_amount - $confirmedPaid);
        $invoice->update([
            'status' => $balance <= 0 ? 'paid' : ($confirmedPaid > 0 ? 'partial' : 'pending'),
        ]);

        return redirect()->route('tenant.parent.fees.index', ['student_id' => $ward->id])
            ->with('success', __('Payment instruction captured. Funds will clear to the school within :hours hours if approved.', ['hours' => 24]))
            ->with('info', __('Reference: :reference', ['reference' => $payment->reference]));
    }

    public function download(Request $request): StreamedResponse
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile()->with('students')->first();
        $wards = $parentProfile?->students ?? collect();

        $wardId = (int) $request->input('student_id');
        $ward = $wards->firstWhere('id', $wardId) ?? $wards->first();

        if (!$ward) {
            abort(404, __('No students linked to your account.'));
        }

        $wardUsers = $this->resolveWardUsers($wards);
        $wardUser = $wardUsers->get($ward->id);

        $fees = $this->loadFeesForStudent($ward, $wardUser);

        $filename = 'fee-statement-' . now()->format('Ymd-His') . '.csv';

        return Response::streamDownload(function () use ($fees, $ward) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Student',
                'Fee Name',
                'Category',
                'Due Date',
                'Status',
                'Assigned Amount',
                'Paid Amount',
                'Outstanding Balance',
                'Source',
            ]);

            foreach ($fees as $item) {
                fputcsv($handle, [
                    $ward->full_name ?? $ward->name,
                    $item['name'],
                    $item['category'] ?? 'General',
                    $item['due_date'] ? $item['due_date']->format('Y-m-d') : 'N/A',
                    $item['status'],
                    number_format($item['amount'], 2, '.', ''),
                    number_format($item['paid'], 2, '.', ''),
                    number_format($item['balance'], 2, '.', ''),
                    $item['source'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function showPayment(FeePayment $payment): View
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile()->with('students')->first();
        $wards = $parentProfile?->students ?? collect();
        $wardUsers = $this->resolveWardUsers($wards);

        if (!$wardUsers->contains(fn ($studentUser) => $studentUser->id === $payment->student_id)) {
            abort(403, __('You are not authorized to view this receipt.'));
        }

        return view('tenant.parent.fees.receipt', [
            'payment' => $payment->load('invoice'),
            'holdUntil' => $this->extractHoldDate($payment),
        ]);
    }

    public function showInvoice(FeeInvoice $invoice): View
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile()->with('students')->first();
        $wards = $parentProfile?->students ?? collect();
        $wardUsers = $this->resolveWardUsers($wards);

        if (!$wardUsers->contains(fn ($studentUser) => $studentUser->id === $invoice->student_id)) {
            abort(403, __('You are not authorized to view this invoice.'));
        }

        return view('tenant.parent.fees.invoice', [
            'invoice' => $invoice->load('payments'),
        ]);
    }

    protected function resolveWardUsers(Collection $wards): Collection
    {
        $emails = $wards->pluck('email')->filter()->unique()->values();

        $users = $emails->isNotEmpty()
            ? User::whereIn('email', $emails)->get()->keyBy('email')
            : collect();

        $map = [];

        foreach ($wards as $student) {
            if (!$student->email) {
                continue;
            }

            $user = $users->get($student->email);
            if ($user) {
                $map[$student->id] = $user;
            }
        }

        return collect($map);
    }

    protected function loadFeesForStudent(Student $student, ?User $studentUser): Collection
    {
        $query = Fee::query()
            ->where('is_active', true)
            ->where(function ($builder) use ($student) {
                $builder->whereHas('assignments', function ($q) use ($student) {
                    $q->where('assignment_type', 'student')
                        ->where('student_id', $student->id)
                        ->where('is_active', true);
                });

                if ($student->class_id) {
                    $builder->orWhereHas('assignments', function ($q) use ($student) {
                        $q->where('assignment_type', 'class')
                            ->where('class_id', $student->class_id)
                            ->where('is_active', true);
                    });
                }
            })
            ->with(['assignments' => function ($q) use ($student) {
                $q->where('is_active', true)
                    ->where(function ($inner) use ($student) {
                        $inner->where(function ($qq) use ($student) {
                            $qq->where('assignment_type', 'student')
                                ->where('student_id', $student->id);
                        });

                        if ($student->class_id) {
                            $inner->orWhere(function ($qq) use ($student) {
                                $qq->where('assignment_type', 'class')
                                    ->where('class_id', $student->class_id);
                            });
                        }
                    });
            }])
            ->orderBy('due_date')
            ->orderBy('name');

        $fees = $query->get();

        if ($fees->isEmpty()) {
            return collect();
        }

        $studentUserId = $studentUser?->id;

        $paymentsByFee = collect();
        if ($studentUserId) {
            $paymentsByFee = FeePayment::where('student_id', $studentUserId)
                ->select('id', 'amount', 'status', 'meta')
                ->get()
                ->reduce(function ($carry, FeePayment $payment) {
                    $feeIds = Arr::wrap(data_get($payment->meta, 'fee_id'));
                    foreach ($feeIds as $feeId) {
                        if (!$feeId) {
                            continue;
                        }
                        $carry[$feeId] = ($carry[$feeId] ?? 0.0) + ($payment->status === 'confirmed' ? (float) $payment->amount : 0.0);
                    }
                    return $carry;
                }, []);
        }

        return $fees->map(function (Fee $fee) use ($student, $studentUserId, $paymentsByFee) {
            $assignment = $fee->assignments
                ->firstWhere('assignment_type', 'student')
                ?: $fee->assignments->firstWhere('assignment_type', 'class')
                ?: $fee->assignments->first();

            $assignedAmount = (float) ($assignment->amount ?? $fee->amount ?? 0);
            $paid = (float) ($paymentsByFee[$fee->id] ?? 0.0);
            $balance = max(0.0, $assignedAmount - $paid);

            $isOverdue = $fee->due_date ? $fee->due_date->isPast() && $balance > 0 : false;
            $isDueSoon = $fee->due_date ? !$fee->due_date->isPast() && $fee->due_date->diffInDays(now()) <= 7 : false;

            return [
                'id' => $fee->id,
                'name' => $fee->name,
                'category' => $fee->category,
                'description' => $fee->description,
                'amount' => $assignedAmount,
                'paid' => $paid,
                'balance' => $balance,
                'status' => $fee->getStatusText(),
                'badge' => $fee->getStatusBadgeClass(),
                'due_date' => $fee->due_date,
                'recurring' => $fee->getRecurringTypeLabel(),
                'source' => $assignment?->assignment_type === 'class'
                    ? __('Class assignment')
                    : __('Direct assignment'),
                'notes' => $assignment?->notes,
                'effective_date' => $assignment?->effective_date,
                'is_overdue' => $isOverdue,
                'is_due_soon' => $isDueSoon,
                'student_user_id' => $studentUserId,
            ];
        });
    }

    protected function extractHoldDate(FeePayment $payment): ?Carbon
    {
        $raw = data_get($payment->meta, 'hold_until');

        if (!$raw) {
            return null;
        }

        try {
            return Carbon::parse($raw);
        } catch (\Throwable $exception) {
            Log::warning('Invalid hold_until meta on fee payment', [
                'payment_id' => $payment->id,
                'value' => $raw,
            ]);

            return null;
        }
    }
}
