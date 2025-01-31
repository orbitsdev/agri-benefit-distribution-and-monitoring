<table align="left">
    <thead>
        <tr style="background-color: #1d4ed8; color: white;">
            <th align="left" width="40" style="background-color: #106c3b; color: white;">ID</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Title</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Distribution Date</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Location</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Status</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Description</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($distributions as $distribution)
            <tr>
                <td align="left" width="40">{{ $distribution->id }}</td>
                <td align="left" width="40">{{ $distribution->title }}</td>
                <td align="left" width="40">{{ \Carbon\Carbon::parse($distribution->distribution_date)->format('F j, Y') }}</td>
                <td align="left" width="40">{{ $distribution->location ?? 'N/A' }}</td>
                <td align="left" width="40">{{ $distribution->status }}</td>
                <td align="left" width="40">{{ $distribution->description ?? 'N/A' }}</td>
                <td align="left" width="40">{{ \Carbon\Carbon::parse($distribution->created_at)->format('F j, Y H:i A') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
