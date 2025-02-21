<div>
    <header class="p-4 border-sm bg-white rounded-lg">
        <div class="mx-auto max-w-7xl">
            <h1 class="text-3xl text-main tracking-tight text-gray-900 ">
                {{ $record->title }}
            </h1>


            <!-- Distribution Details -->
            <p class="text-gray-600 text-sm mt-1 ">
                <span class="font-semibold">Date:</span> {{ $record->distribution_date->format('F d, Y') }} |

                <span class="font-semibold">Location:</span> {{ $record->location }} |
                <span class="font-semibold">Total Beneficiaries:</span> {{ $this->progressData['total'] ?? 0 }} |
                <span class="font-semibold">Claimed:</span> {{ $this->progressData['claimed'] ?? 0 }} /
                <span class="font-semibold">Remaining:</span> {{ $this->progressData['remaining'] ?? 0 }}
            </p>

        </div>
        <div class="mt-2"></div>

        <!-- Include Progress Bar Component -->
        <livewire:distributions.distribution-progress :distributionId="$record->id" />
    </header>

      <div class="mt-8"></div>
    {{ $this->table }}
</div>
