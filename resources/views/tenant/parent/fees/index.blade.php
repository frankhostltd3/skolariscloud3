@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.parent._sidebar')
@endsection

@section('title', __('Fees & Payments'))

@section('content')
@php
    $guardianName = auth()->user()->name ?? __('Guardian');
    $studentName = $selectedWard?->full_name ?? $selectedWard?->name;
    $escrowHoldHours = $escrowHoldHours ?? 24;
@endphp

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">{{ __('Fees and payments') }}</h2>
        <p class="text-muted mb-0">{{ __('Keep track of balances, due dates, and payment receipts without leaving your parent dashboard.') }}</p>
    </div>
    <div class="text-md-end">
        <span class="badge bg-success bg-opacity-10 text-success fw-semibold">{{ __('Welcome, :name', ['name' => $guardianName]) }}</span>
    </div>
</div>

@if($wards->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">{{ __('No linked students yet') }}</h5>
            <p class="text-muted mb-0">{{ __('Once the school links students to your account, their fee breakdowns and payment history will appear here.') }}</p>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tenant.parent.fees.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="student_id" class="form-label fw-semibold">{{ __('Select child') }}</label>
                    <select name="student_id" id="student_id" class="form-select" onchange="this.form.submit()">
                        @foreach($wards as $ward)
                            <option value="{{ $ward->id }}" {{ optional($selectedWard)->id === $ward->id ? 'selected' : '' }}>
                                {{ $ward->full_name ?? $ward->name }}
                                @if($ward->class?->name)
                                    - {{ $ward->class->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold">{{ __('Escrow window') }}</label>
                    <div class="form-control bg-light">{{ $escrowHoldHours }} {{ __('hours') }}</div>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold">{{ __('Statements') }}</label>
                    <a class="btn btn-outline-parent w-100" href="{{ route('tenant.parent.fees.download', ['student_id' => $selectedWard?->id]) }}">
                        <i class="fas fa-file-download me-1"></i>{{ __('Download CSV') }}
                    </a>
                </div>
                <div class="col-12 col-md-4">
                    <div class="alert alert-info mb-0" role="alert">
                        <div class="fw-semibold mb-1">{{ __('Clearing process') }}</div>
                        <p class="mb-0 small">{{ __('Payments entered from this dashboard are held in escrow for up to :hours hours before the school receives them. You can review receipts below while the transaction matures.', ['hours' => $escrowHoldHours]) }}</p>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="small text-white-75 mb-1">{{ __('Total assigned') }}</div>
                    <div class="display-6 fw-bold">{{ format_money($totals['assigned'] ?? 0) }}</div>
                    <div class="small text-white-75">{{ __('Across all fee items for the selected child') }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold text-muted">{{ __('Outstanding balance') }}</span>
                        <span class="badge bg-danger bg-opacity-10 text-danger">{{ $totals['overdue'] }} {{ __('overdue') }}</span>
                    </div>
                    <div class="display-6 fw-bold">{{ format_money($totals['outstanding'] ?? 0) }}</div>
                    <div class="small text-muted">{{ __('Includes overdue and upcoming balances') }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold text-muted">{{ __('Paid so far') }}</span>
                        <span class="badge bg-success bg-opacity-10 text-success">{{ $totals['upcoming'] }} {{ __('upcoming') }}</span>
                    </div>
                    <div class="display-6 fw-bold">{{ format_money($totals['paid'] ?? 0) }}</div>
                    <div class="small text-muted">{{ __('Confirmed payments recorded for this student') }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="fw-semibold text-muted mb-2">{{ __('Family overview') }}</div>
                    <div class="d-flex flex-column gap-2">
                        @foreach($wardSummaries as $summary)
                            <div class="border rounded-3 p-2">
                                <div class="fw-semibold">{{ $summary['ward']->full_name ?? $summary['ward']->name }}</div>
                                <div class="small text-muted">{{ __('Outstanding: :amount', ['amount' => format_money($summary['total_outstanding'])]) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$selectedWard)
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-25 p-3 text-warning">
                        <i class="fas fa-info"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">{{ __('Select a child to view detailed fee information') }}</h5>
                        <p class="text-muted mb-0">{{ __('Use the dropdown above to focus on one student and explore balances, due dates, and payment receipts.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2 text-success"></i>{{ __('Assigned fee items for :student', ['student' => $studentName]) }}</h5>
                <span class="badge bg-light text-dark">{{ $feeItems->count() }} {{ __('items') }}</span>
            </div>
            <div class="card-body">
                @if($feeItems->isEmpty())
                    <div class="text-center py-4 text-muted">{{ __('No active fee assignments for this child at the moment.') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Fee') }}</th>
                                    <th>{{ __('Due date') }}</th>
                                    <th>{{ __('Assigned') }}</th>
                                    <th>{{ __('Paid') }}</th>
                                    <th>{{ __('Outstanding') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($feeItems as $item)
                                    @php
                                        $dueLabel = $item['due_date'] ? $item['due_date']->format('M j, Y') : __('No due date');
                                        $isOverdue = $item['is_overdue'];
                                        $isDueSoon = $item['is_due_soon'];
                                        $statusClass = $isOverdue ? 'danger' : ($isDueSoon ? 'warning' : 'success');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $item['name'] }}</div>
                                            <div class="small text-muted">{{ $item['source'] }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $dueLabel }}</span>
                                        </td>
                                        <td>{{ format_money($item['amount']) }}</td>
                                        <td>{{ format_money($item['paid']) }}</td>
                                        <td>{{ format_money($item['balance']) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }}">
                                                {{ $item['balance'] > 0 ? ($isOverdue ? __('Overdue') : ($isDueSoon ? __('Due soon') : __('Open'))) : __('Settled') }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @if($item['balance'] > 0 && $selectedWardUser)
                                                <form method="POST" action="{{ route('tenant.parent.fees.pay', $item['id']) }}" class="d-flex justify-content-end gap-2">
                                                    @csrf
                                                    <input type="hidden" name="student_id" value="{{ $selectedWard->id }}">
                                                    <input type="number" name="amount" min="0.5" step="0.01" max="{{ number_format($item['balance'], 2, '.', '') }}" value="{{ number_format($item['balance'], 2, '.', '') }}" class="form-control form-control-sm w-auto" required>
                                                    <select name="payment_method" class="form-select form-select-sm w-auto" required>
                                                        <option value="" disabled selected>{{ __('Method') }}</option>
                                                        @foreach($activeGateways as $gateway)
                                                            <option value="{{ $gateway->gateway }}">{{ $gateway->display_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-paper-plane me-1"></i>{{ __('Pay') }}
                                                    </button>
                                                </form>
                                            @elseif(!$selectedWardUser)
                                                <span class="badge bg-secondary">{{ __('Portal link pending') }}</span>
                                            @else
                                                <span class="text-success fw-semibold">{{ __('Paid') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-wallet me-2 text-success"></i>{{ __('Available payment methods') }}</h5>
                        <span class="badge bg-light text-dark">{{ $activeGateways->count() }}</span>
                    </div>
                    <div class="card-body">
                        @if($activeGateways->isEmpty())
                            <div class="text-center py-4 text-muted">{{ __('The school has not enabled online payment methods yet. Please contact accounts for manual settlement options.') }}</div>
                        @else
                            <div class="d-grid gap-3">
                                @foreach($activeGateways as $gateway)
                                    @php
                                        $instructions = data_get($gateway->settings, 'instructions');
                                    @endphp
                                    <div class="border rounded-3 p-3 d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-semibold">{{ $gateway->display_name }}</div>
                                            <div class="small text-muted">{{ __('Gateway code: :code', ['code' => strtoupper((string) $gateway->gateway)]) }}</div>
                                            @if($instructions)
                                                <div class="small text-muted mt-2">{{ $instructions }}</div>
                                            @endif
                                        </div>
                                        <span class="badge bg-success bg-opacity-10 text-success">{{ $gateway->is_test_mode ? __('Test mode') : __('Active') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2 text-success"></i>{{ __('Recent payments (last 20)') }}</h5>
                        <span class="badge bg-light text-dark">{{ $paymentHistory->count() }}</span>
                    </div>
                    <div class="card-body">
                        @if($paymentHistory->isEmpty())
                            <div class="text-center py-4 text-muted">{{ __('Payments will appear here once you submit a transaction through the portal.') }}</div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($paymentHistory as $payment)
                                    @php
                                        $statusClass = $payment->status === 'confirmed' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning');
                                        $holdUntil = data_get($payment->meta, 'hold_until');
                                    @endphp
                                    <a href="{{ route('tenant.parent.fees.payments.show', $payment) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-semibold">{{ format_money($payment->amount) }} <span class="text-muted">{{ __('via') }} {{ strtoupper((string) $payment->method) }}</span></div>
                                            <div class="small text-muted">{{ optional($payment->paid_at)->format('M j, Y g:i A') ?? __('Pending timestamp') }}</div>
                                            @php
                                                $holdLabel = null;
                                                if ($holdUntil) {
                                                    try {
                                                        $holdLabel = \Illuminate\Support\Carbon::parse($holdUntil)->format('M j, Y g:i A');
                                                    } catch (\Throwable $exception) {
                                                        $holdLabel = null;
                                                    }
                                                }
                                            @endphp
                                            @if($holdLabel)
                                                <div class="small text-muted">{{ __('Escrow clears by :time', ['time' => $holdLabel]) }}</div>
                                            @endif
                                        </div>
                                        <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }}">{{ ucfirst($payment->status) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2 text-success"></i>{{ __('Invoices and receipts') }}</h5>
                <span class="badge bg-light text-dark">{{ $invoices->count() }}</span>
            </div>
            <div class="card-body">
                @if($invoices->isEmpty())
                    <div class="text-center py-4 text-muted">{{ __('Invoices will appear here after the first payment instruction is generated.') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Invoice') }}</th>
                                    <th>{{ __('Due date') }}</th>
                                    <th>{{ __('Total amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Payments applied') }}</th>
                                    <th class="text-end">{{ __('View') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                    @php
                                        $statusClass = $invoice->status === 'paid' ? 'success' : ($invoice->status === 'partial' ? 'warning' : 'secondary');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ __('Invoice #:number', ['number' => $invoice->id]) }}</div>
                                            <div class="small text-muted">{{ optional($invoice->created_at)->format('M j, Y') }}</div>
                                        </td>
                                        <td>{{ optional($invoice->due_date)->format('M j, Y') ?? __('Not set') }}</td>
                                        <td>{{ format_money($invoice->total_amount) }}</td>
                                        <td><span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }}">{{ ucfirst($invoice->status) }}</span></td>
                                        <td>
                                            <ul class="list-unstyled mb-0 small">
                                                @foreach($invoice->payments as $payment)
                                                    <li>
                                                        <a href="{{ route('tenant.parent.fees.payments.show', $payment) }}" class="text-decoration-none">
                                                            {{ format_money($payment->amount) }} - {{ ucfirst($payment->status) }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="text-end">
                                            <a class="btn btn-outline-parent btn-sm" href="{{ route('tenant.parent.fees.invoices.show', $invoice) }}">
                                                <i class="fas fa-eye me-1"></i>{{ __('Open') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endif
@endsection

