<table align="left">
    <thead>
        <tr style="background-color: #1d4ed8; color: white;">
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Code</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Title</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Distribution Date</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Location</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Status</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Description</th>
            {{-- <th align="left" width="40" style="background-color: #106c3b; color: white;">Created At</th> --}}
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Items</th>
            <th align="left" width="40" style="background-color: #106c3b; color: white;">Supports</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($distributions as $distribution)
            <tr>
                <!-- Distribution Details -->
                <td align="left" width="40" style="vertical-align: top;">{{ $distribution->code }}</td>
                <td align="left" width="40" style="vertical-align: top;">{{ $distribution->title }}</td>
                <td align="left" width="40" style="vertical-align: top;">{{ \Carbon\Carbon::parse($distribution->distribution_date)->format('F j, Y') }}</td>
                <td align="left" width="40" style="vertical-align: top;">{{ $distribution->location ?? 'N/A' }}</td>
                <td align="left" width="40" style="vertical-align: top;">{{ $distribution->status }}</td>
                <td align="left" width="40" style="vertical-align: top;">{{ $distribution->description ?? 'N/A' }}</td>
                {{-- <td align="left" width="40" style="vertical-align: top;">{{ \Carbon\Carbon::parse($distribution->created_at)->format('F j, Y H:i A') }}</td> --}}

                <!-- Distribution Items -->
                <td align="left" width="40" style="vertical-align: top;">
                    <ol>
                        @foreach ($distribution->distributionItems as $index => $item)
                            <li>
                                Item Name: {{ $item->item->name ?? 'N/A' }}<br>
                                Original Quantity: {{ $item->original_quantity }}<br>
                                Distributed Quantity: {{ $item->quantity }}<br>
                                Remaining Quantity: {{ $item->original_quantity - $item->quantity }}
                            </li>
                        @endforeach
                    </ol>
                </td>

                <!-- Supports -->
                <td align="left" width="40" style="vertical-align: top;">
                    <ol>
                        @foreach ($distribution->supports as $index => $support)
                            <li>
                                Name: {{ $support->personnel->user->name ?? 'N/A' }}<br>
                                Role: {{ $support->type ?? 'N/A' }}<br>
                                Contact: {{ $support->personnel->contact_number ?? 'N/A' }}
                            </li>
                        @endforeach
                    </ol>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
