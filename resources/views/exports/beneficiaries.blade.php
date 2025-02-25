<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Beneficiaries Export</title>
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
                <th align="left" width="40">Beneficiary Code</th>
                <th align="left" width="40">Name</th>
                <th align="left" width="40">Email</th>
                <th align="left" width="40">Contact</th>
                <th align="left" width="40">Address</th>
                <th align="left" width="40">Status</th>
                <th align="left" width="40">Distribution Title</th>
                <th align="left" width="40">Distribution Description</th>
                <th align="left" width="40">Distribution Date</th>
                <th align="left" width="40">Distribution Item Name</th>
                <th align="left" width="40">Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($beneficiaries as $beneficiary)
                <tr>
                    <td>{{ $beneficiary->code }}</td>
                    <td>{{ $beneficiary->name }}</td>
                    <td>{{ $beneficiary->email }}</td>
                    <td>{{ $beneficiary->contact }}</td>
                    <td>{{ $beneficiary->address }}</td>
                    <td>{{ $beneficiary->status }}</td>
                    <td>
                        @if($beneficiary->distributionItem && $beneficiary->distributionItem->distribution)
                            {{ $beneficiary->distributionItem->distribution->title }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($beneficiary->distributionItem && $beneficiary->distributionItem->distribution)
                            {{ $beneficiary->distributionItem->distribution->description }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($beneficiary->distributionItem && $beneficiary->distributionItem->distribution)
                            {{ \Carbon\Carbon::parse($beneficiary->distributionItem->distribution->distribution_date)->format('Y-m-d H:i') }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($beneficiary->distributionItem && $beneficiary->distributionItem->item)
                            {{ $beneficiary->distributionItem->item->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($beneficiary->created_at)->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
