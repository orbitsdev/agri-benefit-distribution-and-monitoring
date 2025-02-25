<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Distribution Items Export</title>
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
    </style>
</head>
<body>
    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th align="left" width="40">ID</th>
                <th align="left" width="40">Item Name</th>
                <th align="left" width="40">Distribution Title</th>
                <th align="left" width="40">Quantity</th>
                <th align="left" width="40">Original Quantity</th>
                {{-- <th align="left" width="40">Is Locked</th> --}}
                <th align="left" width="40">Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($distributionItems as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>
                        @if($item->item)
                            {{ $item->item->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($item->distribution)
                            {{ $item->distribution->title }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->original_quantity }}</td>
                    {{-- <td>{{ $item->is_locked ? 'Yes' : 'No' }}</td> --}}
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
