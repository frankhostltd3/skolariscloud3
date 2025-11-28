<!DOCTYPE html>
<html>

<head>
    <title>Receipt</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .details {
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>OFFICIAL RECEIPT</h1>
        <p>Date: {{ date('Y-m-d') }}</p>
    </div>
    <div class="details">
        <p><strong>Student Name:</strong> {{ $paymentData['name'] }}</p>
        <p><strong>Reference:</strong> {{ $paymentData['tx_ref'] }}</p>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>School Fees Payment</td>
                <td>{{ $paymentData['currency'] }} {{ $paymentData['amount'] }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
