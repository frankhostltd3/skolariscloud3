<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Quicksand', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border: 1px solid #ddd;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #007bff;
        }

        .school-logo {
            max-width: 150px;
            max-height: 80px;
            margin-bottom: 15px;
        }

        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }

        .school-address {
            font-size: 12px;
            color: #666;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            color: #333;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .invoice-to,
        .invoice-info {
            width: 48%;
        }

        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            color: #007bff;
        }

        .info-row {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: 600;
            display: inline-block;
            width: 120px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }

        .items-table th {
            background: #007bff;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .items-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .totals {
            margin-top: 30px;
            float: right;
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .total-row.grand-total {
            background: #007bff;
            color: white;
            font-weight: bold;
            font-size: 18px;
            border: none;
        }

        .payment-history {
            clear: both;
            margin-top: 40px;
        }

        .notes {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-paid {
            background: #28a745;
            color: white;
        }

        .status-partial {
            background: #17a2b8;
            color: white;
        }

        .status-unpaid {
            background: #ffc107;
            color: #333;
        }

        .status-overdue {
            background: #dc3545;
            color: white;
        }

        .status-cancelled {
            background: #6c757d;
            color: white;
        }

        @media print {
            body {
                padding: 0;
            }

            .invoice-container {
                border: none;
                box-shadow: none;
            }

            .no-print {
                display: none;
            }
        }

        .print-button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button onclick="window.print()" class="print-button">
            <i class="bi bi-printer"></i> Print Invoice
        </button>
        <button onclick="window.close()" class="print-button" style="background: #6c757d;">
            Close
        </button>
    </div>

    <div class="invoice-container">
        <!-- Header with School Info -->
        <div class="header">
            @if (isset($logoPath) && $logoPath)
                <img src="{{ $logoPath }}" alt="{{ $invoice->school->name }}" class="school-logo">
            @endif
            <div class="school-name">{{ $invoice->school->name }}</div>
            <div class="school-address">
                {{ setting('school_address', 'School Address') }}<br>
                {{ setting('school_phone', 'Phone') }} | {{ setting('school_email', 'Email') }}
            </div>
        </div>

        <!-- Invoice Title -->
        <div class="invoice-title">INVOICE</div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="invoice-to">
                <div class="section-title">Invoice To:</div>
                <div class="info-row">
                    <div style="font-size: 16px; font-weight: 600; margin-bottom: 5px;">
                        {{ $invoice->student->name }}
                    </div>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    {{ $invoice->student->email ?? 'N/A' }}
                </div>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    {{ $invoice->student->phone ?? 'N/A' }}
                </div>
            </div>

            <div class="invoice-info">
                <div class="info-row">
                    <span class="info-label">Invoice #:</span>
                    <strong>{{ $invoice->invoice_number }}</strong>
                </div>
                <div class="info-row">
                    <span class="info-label">Issue Date:</span>
                    {{ $invoice->issue_date->format('M d, Y') }}
                </div>
                <div class="info-row">
                    <span class="info-label">Due Date:</span>
                    {{ $invoice->due_date->format('M d, Y') }}
                </div>
                <div class="info-row">
                    <span class="info-label">Academic Year:</span>
                    {{ $invoice->academic_year }}
                </div>
                @if ($invoice->term)
                    <div class="info-row">
                        <span class="info-label">Term:</span>
                        {{ $invoice->term }}
                    </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Description</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <strong>{{ $invoice->feeStructure->fee_name }}</strong>
                        @if ($invoice->feeStructure->description)
                            <br><small style="color: #666;">{{ $invoice->feeStructure->description }}</small>
                        @endif
                    </td>
                    <td style="text-align: right;">{{ formatMoney($invoice->total_amount) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>{{ formatMoney($invoice->total_amount) }}</span>
            </div>
            <div class="total-row">
                <span>Paid:</span>
                <span>{{ formatMoney($invoice->paid_amount) }}</span>
            </div>
            <div class="total-row grand-total">
                <span>Balance Due:</span>
                <span>{{ formatMoney($invoice->balance) }}</span>
            </div>
        </div>

        <!-- Payment History -->
        @if ($invoice->payments->count() > 0)
            <div class="payment-history">
                <div class="section-title">Payment History</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Receipt #</th>
                            <th>Method</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td>{{ $payment->receipt_number }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                <td style="text-align: right;">{{ formatMoney($payment->amount) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Notes -->
        @if ($invoice->notes)
            <div class="notes">
                <div class="section-title">Notes:</div>
                <div>{!! nl2br(e($invoice->notes)) !!}</div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your payment!</p>
            <p>For any queries, please contact us at {{ setting('school_email', 'Email') }} or
                {{ setting('school_phone', 'Phone') }}</p>
            <p style="margin-top: 10px; font-size: 11px;">Generated on {{ now()->format('M d, Y h:i A') }}</p>
        </div>
    </div>
</body>

</html>
