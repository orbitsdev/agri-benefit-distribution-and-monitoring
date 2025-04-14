<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Crops Export</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th {
            background-color: #1d4ed8;
            color: white;
            padding: 8px;
        }
        td {
            padding: 8px;
            border: 1px solid #ccc;
        }
        .header {
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
        }
        .header h1 {
            color: #1d4ed8;
            margin-bottom: 5px;
        }
        .header p {
            margin: 5px 0;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Crops Inventory Report</h1>
        <p><strong>Distribution:</strong> {{ $barangayDistribution->distribution->title }}</p>
        <p><strong>Barangay:</strong> {{ $barangayDistribution->barangay->name }}</p>
        <p><strong>Date Generated:</strong> {{ now()->format('F d, Y h:i A') }}</p>
    </div>
    
    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th align="left" width="40">ID</th>
                <th align="left" width="40">Crop Name</th>
                <th align="left" width="40">Original Stock</th>
                <th align="left" width="40">Current Stock</th>
                <th align="left" width="40">Remaining %</th>
                <th align="left" width="40">Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($crops as $crop)
                <tr>
                    <td>{{ $crop->id }}</td>
                    <td>{{ $crop->name }}</td>
                    <td>{{ $crop->original_stocks }}</td>
                    <td>{{ $crop->updated_stocks }}</td>
                    <td>
                        @if($crop->original_stocks > 0)
                            {{ number_format(($crop->updated_stocks / $crop->original_stocks) * 100, 2) }}%
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($crop->created_at)->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
