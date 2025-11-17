<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leave Types Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Leave Types Report</h1>
        <p>Generated on {{ date('F d, Y H:i') }}</p>
        <p>Total Records: {{ $leaveTypes->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Name</th>
                <th style="width: 15%;">Code</th>
                <th style="width: 15%;" class="text-center">Default Days</th>
                <th style="width: 15%;" class="text-center">Requires Approval</th>
                <th style="width: 30%;">Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaveTypes as $leaveType)
                <tr>
                    <td>{{ $leaveType->name }}</td>
                    <td><code>{{ $leaveType->code }}</code></td>
                    <td class="text-center">{{ $leaveType->default_days }}</td>
                    <td class="text-center">
                        @if($leaveType->requires_approval)
                            <span class="badge badge-success">Yes</span>
                        @else
                            <span class="badge badge-secondary">No</span>
                        @endif
                    </td>
                    <td>{{ $leaveType->description ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No leave types found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the HR Management System</p>
    </div>
</body>
</html>