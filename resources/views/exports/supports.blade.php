<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Support Export</title>
</head>
<body>
    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr style="background-color: #1d4ed8; color: white;">

                <th style="background-color: #1d4ed8; color: white;" align="left" width="40">Name</th>
                <th style="background-color: #1d4ed8; color: white;" align="left" width="40">Distribution ID</th>
                <th style="background-color: #1d4ed8; color: white;" align="left" width="40">Type</th>
                <th style="background-color: #1d4ed8; color: white;" align="left" width="40">Unique Code</th>
                <th style="background-color: #1d4ed8; color: white;" align="left" width="40">Enable Item Scanning</th>
                <th style="background-color: #1d4ed8; color: white;" align="left" width="40">Enable Beneficiary Management</th>
                <th style="background-color: #1d4ed8; color: white;" align="left" width="40">Enable List Access</th>
                <th style="background-color: #1d4ed8; color: white;" align="left" width="40">Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supports as $support)
                <tr>
                    <td>{{ $support->id }}</td>
                    <td>
                        @if($support->personnel)
                            {{ $support->personnel?->user->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $support->distribution?->title}}</td>
                    <td>{{ $support->type }}</td>
                    <td>{{ $support->unique_code ?? 'N/A' }}</td>
                    <td>{{ $support->enable_item_scanning ? 'Yes' : 'No' }}</td>
                    <td>{{ $support->enable_beneficiary_management ? 'Yes' : 'No' }}</td>
                    <td>{{ $support->enable_list_access ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
