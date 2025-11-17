<!DOCTYPE html>
<html>

<head>
    <title>Receipt #{{ $payment->receipt_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #333;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .receipt-info div {
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .total {
            font-size: 18px;
            font-weight: bold;
            background-color: #e9ecef;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">Print
            Receipt</button>
        <button onclick="window.close()"
            style="padding: 10px 20px; background: #6c757d; color: white; border: none; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

    <div class="header">
        <h1>{{ $school->name }}</h1>
        <p>{{ $school->address ?? '' }} {{ $school->city ?? '' }}</p>
        <p>Phone: {{ $school->phone ?? 'N/A' }} | Email: {{ $school->email ?? 'N/A' }}</p>
        <h2 style="margin-top: 20px;">PAYMENT RECEIPT</h2>
    </div>

    <div class="receipt-info">
        <div>
            <strong>Receipt Number:</strong> {{ $payment->receipt_number }}<br>
            <strong>Date:</strong> {{ $payment->payment_date->format('F d, Y') }}<br>
            <strong>Payment Method:</strong> {{ $payment->payment_method_label }}
            @if ($payment->reference_number)
                <br><strong>Reference:</strong> {{ $payment->reference_number }}
            @endif
        </div>
        <div style="text-align: right;">
            <strong>Student Name:</strong> {{ $payment->invoice->student->name }}<br>
            <strong>Student ID:</strong> {{ $payment->invoice->student->student_id ?? 'N/A' }}<br>
            <strong>Invoice Number:</strong> {{ $payment->invoice->invoice_number }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Fee Type</th>
                <th style="text-align: right;">Amount Paid</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $payment->invoice->feeStructure->fee_name }}</td>
                <td>{{ ucfirst($payment->invoice->feeStructure->fee_type) }}</td>
                <td style="text-align: right;">{{ formatMoney($payment->amount) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="2" style="text-align: right;">Total Paid:</td>
                <td style="text-align: right;">{{ formatMoney($payment->amount) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #007bff;">
        <h4 style="margin: 0 0 10px 0;">Invoice Summary</h4>
        <p style="margin: 5px 0;"><strong>Invoice Total:</strong> {{ formatMoney($payment->invoice->total_amount) }}
        </p>
        <p style="margin: 5px 0;"><strong>Total Paid:</strong> {{ formatMoney($payment->invoice->paid_amount) }}</p>
        <p style="margin: 5px 0;"><strong>Balance Remaining:</strong> {{ formatMoney($payment->invoice->balance) }}</p>
        <p style="margin: 5px 0;"><strong>Status:</strong> <span
                style="color: {{ $payment->invoice->status == 'paid' ? 'green' : 'orange' }};">{{ strtoupper($payment->invoice->status) }}</span>
        </p>
    </div>

    @if ($payment->notes)
        <div style="margin-top: 20px;">
            <strong>Notes:</strong> {{ $payment->notes }}
        </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated receipt and does not require a signature.</p>
        <p>Printed on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>

</html>
