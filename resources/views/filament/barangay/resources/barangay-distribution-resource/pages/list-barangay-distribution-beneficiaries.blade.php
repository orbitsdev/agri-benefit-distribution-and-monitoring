<x-filament-panels::page>
    <div class="mb-6">
        <h2 class="text-xl font-bold tracking-tight">
            {{ $record->distribution->title }} - Beneficiaries
        </h2>
        <div class="mt-1 flex items-center gap-4">
            <div class="flex items-center gap-1 text-sm">
                <span class="font-medium">Distribution Date:</span>
                <span>{{ $record->distribution_date->format('M d, Y') }}</span>
            </div>
            <div class="flex items-center gap-1 text-sm">
                <span class="font-medium">Venue:</span>
                <span>{{ $record->location }}</span>
            </div>
            <div class="flex items-center gap-1 text-sm">
                <span class="font-medium">Status:</span>
                @if($record->is_disbursed)
                    <span class="inline-flex items-center gap-1 rounded-full bg-success-500 px-2 py-1 text-xs font-medium text-white">
                        <x-heroicon-o-check-circle class="h-3 w-3" />
                        Disbursed
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-danger-500 px-2 py-1 text-xs font-medium text-white">
                        <x-heroicon-o-x-circle class="h-3 w-3" />
                        Not Disbursed
                    </span>
                @endif
            </div>
        </div>
    </div>

    @if(!$record->is_disbursed)
        <div class="mb-6 rounded-lg border border-warning-600 bg-warning-50 p-4 text-warning-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning-600" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-warning-700">Distribution Not Yet Disbursed</h3>
                    <div class="mt-1 text-sm text-warning-700">
                        <p>
                            This distribution has not been marked as disbursed yet. Beneficiaries will not be able to claim their benefits until the distribution is disbursed.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{ $this->table }}
</x-filament-panels::page>
