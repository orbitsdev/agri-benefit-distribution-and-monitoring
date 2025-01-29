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
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Items</h2>
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="text-left text-sm text-gray-500 bg-gray-100">
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Quantity</th>
                        <th class="px-4 py-2">Beneficiaries</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($record->distributionItems as $distributionItem)
                    <tr>
                        <td class="border-t px-4 py-2">{{ $distributionItem->item->name }}</td>
                        <td class="border-t px-4 py-2">{{ $distributionItem->original_quantity }}</td>
                        <td class="border-t px-4 py-2">{{ $distributionItem->getTotalBeneficiaries() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="border-t px-4 py-2 text-center text-gray-500">
                            No distribution items available.
                        </td>
                    </tr>
                @endforelse
                
                    
                </tbody>
            </table>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Supports</h2>
            <ul role="list" class="divide-y divide-gray-100">
                @forelse ($record->supports as $support)
                    <li class="flex justify-between gap-x-6 py-5">
                        <div class="flex min-w-0 gap-x-4">
                            <img class="w-10 h-10 flex-none rounded-full bg-gray-50" src="{{$support->personnel->user->getImage()}}" alt="{{$support->personnel->user->name }}">
                            <div class="min-w-0 flex-auto">
                                <p class="text-sm font-semibold text-gray-900">{{ $support->personnel->user->name }}</p>
                                <p class="mt-1 truncate text-xs text-gray-500">{{ $support->personnel->user->email }} / {{ $support->personnel->contact_number }}</p>
                            </div>
                        </div>
                        <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                            <p class="text-sm text-gray-900">{{ $support->type }}</p>
                        </div>
                    </li>
                @empty
                    <li class="py-5 text-center text-gray-500">
                        No supports available.
                    </li>
                @endforelse
            </ul>
        </div>
        
    </div>
    
</div>