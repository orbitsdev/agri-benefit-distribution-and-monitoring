<div class="p-4">
    <h2 class="text-lg font-semibold mb-4">Import Failures</h2>

    @if ($importFailures->isEmpty())
        <p class="text-gray-600">No import errors found.</p>
    @else
        <table class="min-w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-4 py-2">#</th>
                    <th class="border border-gray-300 px-4 py-2">Row Data</th>
                    <th class="border border-gray-300 px-4 py-2">Error Message</th>
                    <th class="border border-gray-300 px-4 py-2">Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($importFailures as $index => $failure)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">{{ $index + 1 }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <pre class="text-sm bg-gray-50 p-2 rounded">{{ json_encode(json_decode($failure->row_data), JSON_PRETTY_PRINT) }}</pre>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">{{ $failure->error_message }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $failure->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
