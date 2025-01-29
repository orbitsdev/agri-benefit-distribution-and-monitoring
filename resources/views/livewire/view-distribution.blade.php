<div>
    <div class="container ">
       
    
        <!-- Section: Distribution Details -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">Title</p>
                    <p class="text-lg font-medium text-gray-800">{{$record->title}}</p>
                </div>
                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{$record->status}}</span>
            </div>
            <div class="grid grid-cols-2 gap-6 mt-4">
                <div>
                    <p class="text-sm text-gray-500">Distribution Date</p>
                    <p class="text-base text-gray-800">{{ \Carbon\Carbon::parse($record->distribution_date)->format('F j, Y') }}</p>

                </div>
                <div>
                    <p class="text-sm text-gray-500">Location</p>
                    <p class="text-base text-gray-800">{{$record->location}}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Description</p>
                    <p class="text-base text-gray-800">{{$record->description}}</p>
                </div>
                {{-- <div>`
                    <p class="text-sm text-gray-500">Lock Status</p>
                    <p class="text-base text-gray-800">Locked</p>
                </div> --}}
            </div>
        </div>
    
        <!-- Section: Distribution Items -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Distribution Items</h2>
            <div class="divide-y divide-gray-200">

                @foreach ($record->distributionItems as $distributionItem)
                    
                <!-- Item 1 -->
                <div class="">
                    <div class="flex justify-between items-center">
                        <p class="text-gray-800 font-medium">{{$distributionItem->item->name}}</p>
                        <span class="text-sm text-gray-500">{{$distributionItem->original_quantity}}</span>
                    </div>
                    {{-- <div class="mt-2">
                        <p class="text-sm text-gray-500">Beneficiaries</p>
                        <ul class="text-gray-700 text-sm mt-1">
                            <li>Juan Dela Cruz - Claimed</li>
                            <li>Maria Santos - Unclaimed</li>
                        </ul>
                    </div> --}}
                </div>
                @endforeach
                <!-- Item 2 -->
            
            </div>
        </div>
    
        <!-- Section: Support -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Support</h2>
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="text-left text-sm text-gray-500 bg-gray-100">
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Position</th>
                        <th class="px-4 py-2">Type</th>
                        <th class="px-4 py-2">Permissions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <tr>
                        <td class="border-t px-4 py-2">John Smith</td>
                        <td class="border-t px-4 py-2">Scanner</td>
                        <td class="border-t px-4 py-2">Logistics</td>
                        <td class="border-t px-4 py-2">Item Scanning, List Access</td>
                    </tr>
                    <tr>
                        <td class="border-t px-4 py-2">Jane Doe</td>
                        <td class="border-t px-4 py-2">Registrar</td>
                        <td class="border-t px-4 py-2">Support</td>
                        <td class="border-t px-4 py-2">Beneficiary Management</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
</div>