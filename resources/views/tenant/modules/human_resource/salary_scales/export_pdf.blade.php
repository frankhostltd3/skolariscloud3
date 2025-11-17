<!DOCTYPE html>
<html>
<head>
    <title>Salary Scales Export</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Salary Scales</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Grade</th>
                <th>Min Amount</th>
                <th>Max Amount</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salaryScales as $scale)
            <tr>
                <td>{{ $scale->id }}</td>
                <td>{{ $scale->name }}</td>
                <td>{{ $scale->grade }}</td>
                <td>{{ number_format($scale->min_amount) }}</td>
                <td>{{ number_format($scale->max_amount) }}</td>
                <td>{{ $scale->notes }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
