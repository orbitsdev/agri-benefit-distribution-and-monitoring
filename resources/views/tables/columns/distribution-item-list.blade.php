<div class="py-2 text-sm">
 

            
    
        
    <ul>
        @forelse ($getRecord()->distributionItems as $index => $distributionItem)
            <li class="">
                <span>{{ $distributionItem->item->name }}</span> | <span>{{ $distributionItem->quantity }} | {{$distributionItem->getTotalBeneficiaries()}}</span>
            </li>
        @empty
            <li class="text-center py-4">
                <p>No distribution items available.</p>
            </li>
        @endforelse
    </ul>


</div>
