<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transactions Export</title>
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
                <th align="left" width="40">Transaction ID</th>
                <th align="left" width="40">Barangay</th>
                <th align="left" width="40">Distribution Title</th>
                <th align="left" width="40">Distribution Code</th>
                <th align="left" width="40">Distribution Status</th>
                <th align="left" width="40">Distribution Date</th>
                <th align="left" width="40">Distribution Item</th>
                <th align="left" width="40">Beneficiary Name</th>
                <th align="left" width="40">Beneficiary Code</th>
                <th align="left" width="40">Support Personnel</th>
                <th align="left" width="40">Support Type</th>
                <th align="left" width="40">Unique Code</th>
                <th align="left" width="40">Admin</th>
                <th align="left" width="40">Action</th>
                <th align="left" width="40">Role</th>
                <th align="left" width="40">Performed At</th>
                <th align="left" width="40">Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->id }}</td>
                    <td>
                        @if($transaction->barangay)
                            {{ $transaction->barangay->name }}
                        @else
                            {{ $transaction->barangay_details['name'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if($transaction->distribution)
                            {{ $transaction->distribution->title }}
                        @else
                            {{ $transaction->distribution_details['title'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>{{ $transaction->distribution_details['code'] ?? 'N/A' }}</td>
                    <td>{{ $transaction->distribution_details['status'] ?? 'N/A' }}</td>
                    <td>{{ $transaction->distribution_details['date'] ?? 'N/A' }}</td>
                    <td>{{ $transaction->distribution_item_details['name'] ?? 'N/A' }}</td>
                    <td>
                        {{ $transaction->beneficiary_details['name'] ?? ($transaction->beneficiary ? $transaction->beneficiary->name : 'N/A') }}
                    </td>
                    <td>{{ $transaction->beneficiary_details['code'] ?? 'N/A' }}</td>
                    <td>
                        {{ $transaction->support_details['name'] ?? ($transaction->support && $transaction->support->personnel ? $transaction->support->personnel->user->name : 'N/A') }}
                    </td>
                    <td>{{ $transaction->support_details['type'] ?? 'N/A' }}</td>
                    <td>{{ $transaction->support_details['unique_code'] ?? 'N/A' }}</td>
                    <td>
                        @if($transaction->admin)
                            {{ $transaction->admin->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $transaction->action ?? 'N/A' }}</td>
                    <td>{{ $transaction->role ?? 'N/A' }}</td>
                    <td>{{ $transaction->performed_at ? \Carbon\Carbon::parse($transaction->performed_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                    <td>{{ $transaction->created_at ? \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d H:i') : 'N/A' }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
