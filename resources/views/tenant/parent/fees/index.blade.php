@extends('layouts.tenant.parent')

@section('title', __('Fees & Payments'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">{{ __('Fees & Payments') }}</h4>
    </div>

    <div class="row g-4">
        @forelse ($students as $student)
            @php
                $invoices = $student->invoices ?? collect([]);
                $totalDue = $invoices->sum('total_amount');
                $totalPaid = $invoices->sum('paid_amount');
                $balance = $invoices->sum('balance');
            @endphp
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            @if ($student->profile_photo)
                                <img src="{{ $student->profile_photo_url }}" alt="{{ $student->name }}"
                                    class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center me-2"
                                    style="width: 40px; height: 40px;">
                                    <span class="fw-bold text-muted">{{ substr($student->name ?? 'S', 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $student->name ?? 'Student' }}</h6>
                                <small class="text-muted">{{ optional($student->class)->name ?? 'No Class' }}</small>
                            </div>
                        </div>
                        <div>
                            @if ($balance > 0)
                                <span class="badge bg-danger fs-6">{{ __('Outstanding') }}:
                                    {{ formatMoney($balance) }}</span>
                                <a href="{{ route('tenant.finance.payments.pay', ['student_id' => $student->id]) }}"
                                    class="btn btn-sm btn-primary ms-2">
                                    {{ __('Pay Now') }}
                                </a>
                            @else
                                <span class="badge bg-success fs-6">{{ __('Fully Paid') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">{{ __('Invoice #') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Due Date') }}</th>
                                        <th class="text-end">{{ __('Amount') }}</th>
                                        <th class="text-end">{{ __('Paid') }}</th>
                                        <th class="text-end">{{ __('Balance') }}</th>
                                        <th class="text-end pe-4">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($invoices->sortByDesc('created_at') as $invoice)
                                        <tr>
                                            <td class="ps-4 fw-bold">{{ $invoice->invoice_number ?? '-' }}</td>
                                            <td>{{ optional($invoice->feeStructure)->fee_name ?? 'Tuition Fee' }}</td>
                                            <td>{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : '-' }}
                                            </td>
                                            <td class="text-end">{{ formatMoney($invoice->total_amount) }}</td>
                                            <td class="text-end text-success">{{ formatMoney($invoice->paid_amount) }}</td>
                                            <td class="text-end fw-bold">{{ formatMoney($invoice->balance) }}</td>
                                            <td class="text-end pe-4">
                                                @if ($invoice->status == 'paid')
                                                    <span class="badge bg-success">{{ __('Paid') }}</span>
                                                @elseif($invoice->status == 'partial')
                                                    <span class="badge bg-warning">{{ __('Partial') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('Unpaid') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                {{ __('No invoices found for this student.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>{{ __('No children linked to your account.') }}
                </div>
            </div>
        @endforelse
    </div>
@endsection
