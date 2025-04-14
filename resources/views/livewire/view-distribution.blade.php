<div>
    <div class="container ">


        <!-- Section: Distribution Details -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">Title</p>
                    <p class="text-lg font-medium text-gray-800">{{$record->title}}</p>
                </div>

            </div>
            <div class="grid grid-cols-2 gap-6 mt-4">
                <div>
                    <p class="text-sm text-gray-500">Distribution Date</p>
                    <p class="text-base text-gray-800">{{ \Carbon\Carbon::parse($record->distribution_date)->format('F j, Y') }}</p>

                </div>
                {{-- <div>
                    <p class="text-sm text-gray-500">Location</p>
                    <p class="text-base text-gray-800">{{$record->location}}</p>
                </div> --}}
                <div>
                    <p class="text-sm text-gray-500">Description</p>
                    <p class="text-base text-gray-800">{{$record->description}}</p>
                </div>

            </div>
        </div>


        {{-- <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Distribution Items</h2>
            <div class="divide-y divide-gray-200">

                @foreach ($record->distributionItems as $distributionItem)


                <div class="">
                    <div class="flex justify-between items-center">
                        <p class="text-gray-800 font-medium">{{$distributionItem->item->name}}</p>
                        <span class="text-sm text-gray-500">{{$distributionItem->original_quantity}}</span>
                    </div>

                </div>
                @endforeach


            </div>
        </div> --}}

        <!-- Section: Support -->


        <!-- Section: Inventory -->
        <div class="bg-white shadow-md rounded-lg p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Inventory</h2>
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="text-left text-sm text-gray-500 bg-gray-100">
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Original Stocks</th>
                        <th class="px-4 py-2">Updated Stocks</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Beneficiaries</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($record->crops as $crop)
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
                        <td class="border-t px-4 py-2">{{ $crop->cropsToReceive->count() }}</td>
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

        <!-- Section: Barangay Distributions -->
        <div class="bg-white shadow-md rounded-lg p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Barangay Distributions</h2>
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="text-left text-sm text-gray-500 bg-gray-100">
                        <th class="px-4 py-2">Barangay</th>
                        <th class="px-4 py-2">Beneficiaries</th>
                        <th class="px-4 py-2">Claimed/Unclaimed</th>
                        <th class="px-4 py-2">Progress</th>
                        <th class="px-4 py-2">Location & Date</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($record->barangayDistributions as $barangayDistribution)
                    @php
                        $totalBeneficiaries = $barangayDistribution->beneficiaries->count();
                        $claimedCount = 0;
                        $unclaimedCount = 0;

                        foreach ($barangayDistribution->beneficiaries as $beneficiary) {
                            if ($beneficiary->cropsToReceive && $beneficiary->cropsToReceive->is_claimed) {
                                $claimedCount++;
                            } else {
                                $unclaimedCount++;
                            }
                        }

                        $progressPercentage = $totalBeneficiaries > 0 ? round(($claimedCount / $totalBeneficiaries) * 100) : 0;
                    @endphp
                    <tr>
                        <td class="border-t px-4 py-2">{{ $barangayDistribution->barangay->name }}</td>
                        <td class="border-t px-4 py-2">{{ $totalBeneficiaries }}</td>
                        <td class="border-t px-4 py-2 text-sm">
                            <span class="font-medium text-green-700">{{ $claimedCount }}</span>
                            <span class="text-gray-500 mx-1">/</span>
                            <span class="font-medium text-gray-700">{{ $unclaimedCount }}</span>
                        </td>
                        <td class="border-t px-4 py-2">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $progressPercentage }}%</span>
                        </td>
                        <td class="border-t px-4 py-2 text-sm">
                            <div class="font-medium">{{ $barangayDistribution->location ?? 'Location not specified' }}</div>
                            @if($barangayDistribution->distribution_date)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($barangayDistribution->distribution_date)->format('M j, Y') }}
                                </div>
                            @else
                                <div class="text-xs text-gray-400 mt-1">Date not scheduled</div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="border-t px-4 py-2 text-center text-gray-500">
                            No barangay distributions available.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


    </div>

</div>
