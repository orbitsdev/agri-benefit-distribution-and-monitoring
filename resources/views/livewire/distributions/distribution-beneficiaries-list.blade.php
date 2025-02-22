<div x-data="{ tab: 'beneficiaries' }">
    <header class="p-4 border-sm bg-white rounded-lg">
        <div class="mx-auto max-w-7xl">
            <h1 class="text-3xl text-main tracking-tight text-gray-900">
                {{ $record->title }}
            </h1>

            <!-- Distribution Details -->
            <p class="text-gray-600 text-sm mt-1">
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

    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-4" aria-label="Tabs">
            <button
                @click="tab = 'beneficiaries'"
                :class="tab === 'beneficiaries' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium">
                Beneficiaries
            </button>

            <button
                @click="tab = 'transactions'"
                :class="tab === 'transactions' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium">
                Transactions
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="mt-6">
        <!-- Beneficiaries Table -->
        <div x-show="tab === 'beneficiaries'" x-cloak>
            {{ $this->table }}
        </div>

        <!-- Transactions Tab -->
        <div x-show="tab === 'transactions'" x-cloak>
            <livewire:support-distribution-transaction-history :record="$record"  />

            {{-- <p class="text-gray-700 text-lg font-semibold">This is the Transactions Page</p> --}}
        </div>
    </div>
</div>
