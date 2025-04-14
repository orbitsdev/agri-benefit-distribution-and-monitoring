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
                <th align="left" width="40">Action</th>
                <th align="left" width="40">Performed At</th>
                <th align="left" width="40">Barangay</th>
                <th align="left" width="40">Distribution Title</th>
                <th align="left" width="40">Distribution Date</th>
                <th align="left" width="40">Crop</th>
                <th align="left" width="40">Crop Code</th>
                <th align="left" width="40">Beneficiary Name</th>
                <th align="left" width="40">RSBSA No.</th>
                <th align="left" width="40">Contact</th>
                <th align="left" width="40">Email</th>
                <th align="left" width="40">Recorder</th>
                <th align="left" width="40">Recorder Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->id }}</td>
                    <td>{{ $transaction->action }}</td>
                    <td>{{ $transaction->performed_at ? \Carbon\Carbon::parse($transaction->performed_at)->format('Y-m-d H:i') : 'N/A' }}</td>

                    <td>{{ $transaction->barangay_details['name'] ?? 'N/A' }}</td>

                    <td>{{ $transaction->distribution_details['title'] ?? 'N/A' }}</td>
                    <td>{{ isset($transaction->distribution_details['distribution_date']) ?
                        \Carbon\Carbon::parse($transaction->distribution_details['distribution_date'])->format('Y-m-d') : 'N/A' }}</td>

                    <td>{{ $transaction->crops_details['crop_name'] ?? 'N/A' }}</td>
                    <td>{{ $transaction->crops_details['unique_code'] ?? 'N/A' }}</td>

                    <td>
                        @php
                            $firstName = $transaction->beneficiary_details['first_name'] ?? '';
                            $middleName = $transaction->beneficiary_details['middle_name'] ?? '';
                            $lastName = $transaction->beneficiary_details['last_name'] ?? '';
                            $extName = $transaction->beneficiary_details['ext_name'] ?? '';
                            $fullName = trim("$firstName $middleName $lastName $extName");
                        @endphp
                        {{ $fullName ?: 'N/A' }}
                    </td>

                    <td>{{ $transaction->beneficiary_details['rsbsa_no'] ?? 'N/A' }}</td>
                    <td>{{ $transaction->beneficiary_details['contact_num'] ?? 'N/A' }}</td>
                    <td>{{ $transaction->beneficiary_details['email'] ?? 'N/A' }}</td>

                    <td>{{ $transaction->recorder_details['name'] ?? 'N/A' }}</td>
                    <td>{{ $transaction->recorder_details['role'] ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
