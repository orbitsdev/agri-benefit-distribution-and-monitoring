<table align="left">
    <thead>
        <tr style="background-color: #1d4ed8; color: white;">
            <th align="left" width="40" style="background-color: #106c3b; color: white;">ID</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Name</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Email</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Role</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Status</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Barangay</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <td align="left" width="40">{{ $user->id }}</td>
                <td align="left" width="40">{{ $user->name }}</td>
                <td align="left" width="40">{{ $user->email }}</td>
                <td align="left" width="40">{{ $user->role }}</td>
                <td align="left" width="40">{{ $user->is_active ? 'Active' : 'Inactive' }}</td>
                <td align="left" width="40">{{ $user->barangay->name ?? 'N/A' }}</td>
                <td align="left" width="40">{{ \Carbon\Carbon::parse($user->created_at)->format('F j, Y H:i A') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
