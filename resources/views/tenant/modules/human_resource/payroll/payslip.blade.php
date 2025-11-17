<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $payroll->payroll_number }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#222; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        .company h2 { margin:0 0 4px; }
        .meta, .earnings, .deductions { width:100%; border-collapse:collapse; margin-bottom:15px; }
        .meta td { padding:4px 6px; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:6px 8px; border:1px solid #ddd; }
        th { background:#f5f5f5; text-align:left; }
        .right { text-align:right; }
        .section-title { background:#0d6efd; color:#fff; padding:6px 8px; font-size:13px; margin-top:18px; }
        .totals { font-weight:bold; }
        .net-box { border:2px solid #198754; padding:10px; margin-top:10px; background:#f6fff8; }
        .small { font-size:11px; color:#555; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">
            <h2>{{ $organization['name'] }}</h2>
            <div class="small">{{ $organization['address'] }}</div>
            <div class="small">{{ $organization['phone'] }} | {{ $organization['email'] }}</div>
        </div>
        <div style="text-align:right;">
            <h3 style="margin:0 0 4px;">PAYSLIP</h3>
            <div class="small">Payroll #: {{ $payroll->payroll_number }}</div>
            <div class="small">Period: {{ $payroll->period_label }}</div>
            <div class="small">Generated: {{ now()->format('Y-m-d H:i') }}</div>
        </div>
    </div>

    <table class="meta" style="margin-bottom:10px;">
        <tr>
            <td><strong>Employee:</strong> {{ $employee->full_name }}</td>
            <td><strong>Employee No:</strong> {{ $employee->employee_number }}</td>
        </tr>
        <tr>
            <td><strong>Department:</strong> {{ $employee->department?->name ?? '—' }}</td>
            <td><strong>Position:</strong> {{ $employee->position?->title ?? '—' }}</td>
        </tr>
        <tr>
            <td><strong>Payment Date:</strong> {{ $payroll->payment_date->format('Y-m-d') }}</td>
            <td><strong>Status:</strong> {{ ucfirst($payroll->status) }}</td>
        </tr>
    </table>

    @php($currencyCode = currency_code())

    <div class="section-title">EARNINGS</div>
    <table class="earnings">
        <thead><tr><th>Description</th><th class="right">Amount ({{ $currencyCode }})</th></tr></thead>
        <tbody>
            <tr><td>Basic Salary</td><td class="right">{{ format_money($payroll->basic_salary) }}</td></tr>
            <tr><td>Allowances</td><td class="right">{{ format_money($payroll->allowances) }}</td></tr>
            @if($payroll->bonuses > 0)<tr><td>Bonuses</td><td class="right">{{ format_money($payroll->bonuses) }}</td></tr>@endif
            @if($payroll->overtime_pay > 0)<tr><td>Overtime Pay ({{ $payroll->overtime_hours }}h)</td><td class="right">{{ format_money($payroll->overtime_pay) }}</td></tr>@endif
            <tr class="totals"><td>Gross Salary</td><td class="right">{{ format_money($payroll->gross_salary) }}</td></tr>
        </tbody>
    </table>

    <div class="section-title">DEDUCTIONS</div>
    <table class="deductions">
        <thead><tr><th>Description</th><th class="right">Amount ({{ $currencyCode }})</th></tr></thead>
        <tbody>
            <tr><td>Tax (PAYE)</td><td class="right">{{ format_money($payroll->tax_deduction) }}</td></tr>
            <tr><td>NSSF</td><td class="right">{{ format_money($payroll->nssf_deduction) }}</td></tr>
            <tr><td>Health Insurance</td><td class="right">{{ format_money($payroll->health_insurance) }}</td></tr>
            @if($payroll->loan_deduction > 0)<tr><td>Loan Deduction</td><td class="right">{{ format_money($payroll->loan_deduction) }}</td></tr>@endif
            @if($payroll->other_deductions > 0)<tr><td>Other Deductions</td><td class="right">{{ format_money($payroll->other_deductions) }}</td></tr>@endif
            <tr class="totals"><td>Total Deductions</td><td class="right">{{ format_money($payroll->total_deductions) }}</td></tr>
        </tbody>
    </table>

    <div class="net-box">
        <strong>NET SALARY: {{ format_money($payroll->net_salary) }}</strong>
    </div>

    @if($payroll->notes)
        <p class="small" style="margin-top:8px;"><strong>Notes:</strong> {{ $payroll->notes }}</p>
    @endif

    <p class="small" style="margin-top:25px;">This payslip is system generated and valid without signature.</p>
</body>
</html>