<!DOCTYPE html>
<html>
<head>
    <title>Class Summary - {{ $className }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <h1>Class Summary: {{ $className }}</h1>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Student Name</th>
                <th>Total Marks</th>
                <th>Average</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr>
                <td>{{ $report->rank }}</td>
                <td>{{ $report->student->name }}</td>
                <td>{{ $report->total_marks }}</td>
                <td>{{ number_format($report->average_score, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
