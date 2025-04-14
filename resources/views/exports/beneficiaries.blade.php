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
                <th align="left" width="40">RSBSA No.</th>
                <th align="left" width="40">Full Name</th>
                <th align="left" width="40">Email</th>
                <th align="left" width="40">Contact</th>
                <th align="left" width="40">Address</th>
                <th align="left" width="40">Claim Status</th>
                <th align="left" width="40">Date Claimed</th>
                <th align="left" width="40">Crop</th>
                <th align="left" width="40">Crop Code</th>
                <th align="left" width="40">Distribution Title</th>
                <th align="left" width="40">Distribution Date</th>
                <th align="left" width="40">Barangay</th>
                <th align="left" width="40">Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($beneficiaries as $beneficiary)
                <tr>
                    <td>{{ $beneficiary->rsbsa_no ?? 'N/A' }}</td>
                    <td>{{ trim($beneficiary->first_name . ' ' . $beneficiary->middle_name . ' ' . $beneficiary->last_name . ' ' . $beneficiary->ext_name) }}</td>
                    <td>{{ $beneficiary->email ?? 'N/A' }}</td>
                    <td>{{ $beneficiary->contact_num ? '+63' . substr($beneficiary->contact_num, -10) : 'N/A' }}</td>
                    <td>{{ $beneficiary->farmer_address ?? 'N/A' }}</td>
                    <td>
                        @if($beneficiary->cropsToReceive)
                            {{ $beneficiary->cropsToReceive->is_claimed ? 'Claimed' : 'Unclaimed' }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($beneficiary->cropsToReceive && $beneficiary->cropsToReceive->date_claimed)
                            {{ \Carbon\Carbon::parse($beneficiary->cropsToReceive->date_claimed)->format('Y-m-d H:i') }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($beneficiary->cropsToReceive && $beneficiary->cropsToReceive->crop)
                            {{ $beneficiary->cropsToReceive->crop->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($beneficiary->cropsToReceive)
                            {{ $beneficiary->cropsToReceive->unique_code ?? 'N/A' }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($beneficiary->barangayDistribution && $beneficiary->barangayDistribution->distribution)
                            {{ $beneficiary->barangayDistribution->distribution->title }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($beneficiary->barangayDistribution && $beneficiary->barangayDistribution->distribution)
                            {{ \Carbon\Carbon::parse($beneficiary->barangayDistribution->distribution->distribution_date)->format('Y-m-d') }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($beneficiary->barangayDistribution && $beneficiary->barangayDistribution->barangay)
                            {{ $beneficiary->barangayDistribution->barangay->name }}
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
