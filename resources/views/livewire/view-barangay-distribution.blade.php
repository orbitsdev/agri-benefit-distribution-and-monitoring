<div>
    <div class="container">
        <!-- Section: Distribution Details -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">Distribution Title</p>
                    <p class="text-lg font-medium text-gray-800">{{$record->distribution->title}}</p>
                </div>
                <div>
                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                        {{ $record->is_disbursed ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-red-50 text-red-700 ring-red-600/20' }}">
                        {{ $record->is_disbursed ? 'Disbursed' : 'Not Disbursed' }}
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6 mt-4">
                <div>
                    <p class="text-sm text-gray-500">Distribution Date</p>
                    <p class="text-base text-gray-800">{{ \Carbon\Carbon::parse($record->distribution_date)->format('F j, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Location/Venue</p>
                    <p class="text-base text-gray-800">{{$record->location ?? 'Not specified'}}</p>
                </div>
            </div>
        </div>

        <!-- Section: Beneficiary Statistics -->
        <div class="bg-white shadow-md rounded-lg p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Beneficiary Statistics</h2>

            @php
                $totalBeneficiaries = $record->beneficiaries->count();
                $claimedCount = 0;
                $unclaimedCount = 0;

                foreach ($record->beneficiaries as $beneficiary) {
                    if ($beneficiary->cropsToReceive && $beneficiary->cropsToReceive->is_claimed) {
                        $claimedCount++;
                    } else {
                        $unclaimedCount++;
                    }
                }

                $progressPercentage = $totalBeneficiaries > 0 ? round(($claimedCount / $totalBeneficiaries) * 100) : 0;
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500">Total Beneficiaries</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $totalBeneficiaries }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-sm text-green-600">Claimed</p>
                    <p class="text-2xl font-semibold text-green-700">{{ $claimedCount }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500">Unclaimed</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ $unclaimedCount }}</p>
                </div>
            </div>

            <div class="mt-6">
                <p class="text-sm text-gray-500 mb-2">Claim Progress</p>
                <div class="w-full bg-gray-200 rounded-full h-4 relative">
                    @php
                        // Determine color based on percentage
                        $progressColor = 'var(--color-progress-low-600, #dc2626)'; // Default to red (low)
                        $textColor = 'text-white';

                        if ($progressPercentage >= 75) {
                            $progressColor = 'var(--color-progress-high-600, #16a34a)'; // Green for high progress
                        } elseif ($progressPercentage >= 40) {
                            $progressColor = 'var(--color-progress-medium-600, #f59e0b)'; // Amber for medium progress
                        }
                    @endphp
                    <div class="h-4 rounded-full absolute left-0 top-0" style="width: {{ $progressPercentage }}%; background-color: {{ $progressColor }};"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xs font-bold {{ $progressPercentage > 5 ? 'text-white' : 'text-gray-700' }}">{{ $progressPercentage }}%</span>
                    </div>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-xs text-gray-500">0%</span>
                    <span class="text-xs font-medium text-gray-700">Claim Completion</span>
                    <span class="text-xs text-gray-500">100%</span>
                </div>
            </div>
        </div>

        <!-- Section: Inventory -->
        <div class="bg-white shadow-md rounded-lg p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Inventory</h2>
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="text-left text-sm text-gray-500 bg-gray-100">
                        <th class="px-4 py-2">Crop Name</th>
                        <th class="px-4 py-2">Original Stocks</th>
                        <th class="px-4 py-2">Current Stocks</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Beneficiaries</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($record->distribution->crops as $crop)
                    <tr>
                        <td class="border-t px-4 py-2">{{ $crop->name }}</td>
                        <td class="border-t px-4 py-2">{{ $crop->original_stocks }}</td>
                        <td class="border-t px-4 py-2">{{ $crop->updated_stocks }}</td>
                        <td class="border-t px-4 py-2">
                            @if($crop->is_active)
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-600/20">Inactive</span>
                            @endif
                        </td>
                        <td class="border-t px-4 py-2">
                            {{ $crop->cropsToReceive->where('barangay_distribution_id', $record->id)->count() }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="border-t px-4 py-2 text-center text-gray-500">
                            No crops available for this distribution.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Section: Recent Transactions -->
        <div class="bg-white shadow-md rounded-lg p-6 mt-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Recent Transactions</h2>
                @php
                    $transactionCount = \App\Models\Transaction::where('barangay_distribution_id', $record->id)->count();
                @endphp
                @if($transactionCount > 0)
                <a href="{{ \App\Filament\Barangay\Resources\BarangayDistributionResource::getUrl('transaction-history', ['record' => $record->id]) }}"
                   class="text-sm text-blue-600 hover:text-blue-800" target="_blank">
                    View All
                </a>
                @endif
            </div>

            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="text-left text-sm text-gray-500 bg-gray-100">
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Beneficiary</th>
                        <th class="px-4 py-2">Action</th>
                        <th class="px-4 py-2">Recorded By</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @php
                        $recentTransactions = \App\Models\Transaction::where('barangay_distribution_id', $record->id)
                            ->latest('performed_at')
                            ->take(5)
                            ->get();
                    @endphp
                    @forelse ($recentTransactions as $transaction)
                    <tr>
                        <td class="border-t px-4 py-2">{{ \Carbon\Carbon::parse($transaction->performed_at)->format('M d, Y h:i A') }}</td>
                        <td class="border-t px-4 py-2">
                            @php
                                $firstName = $transaction->beneficiary_details['first_name'] ?? '';
                                $middleName = $transaction->beneficiary_details['middle_name'] ?? '';
                                $lastName = $transaction->beneficiary_details['last_name'] ?? '';
                                $extName = $transaction->beneficiary_details['ext_name'] ?? '';
                                $fullName = trim("$firstName $middleName $lastName $extName");
                            @endphp
                            {{ $fullName }}
                        </td>
                        <td class="border-t px-4 py-2">
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium
                                {{ $transaction->action == 'Claimed' ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' : 'bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/20' }}">
                                {{ $transaction->action }}
                            </span>
                        </td>
                        <td class="border-t px-4 py-2">{{ $transaction->recorder_details['name'] ?? 'Unknown' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="border-t px-4 py-2 text-center text-gray-500">
                            No transactions recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
