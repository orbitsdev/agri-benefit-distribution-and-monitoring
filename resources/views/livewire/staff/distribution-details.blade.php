<x-support-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Distribution Details</h2>
                <a href="{{ route('staff.distributions') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Distributions
                </a>
            </div>

            <div x-data="{ tab: 'beneficiaries' }">
                {{-- <header class="p-4 border-sm bg-white rounded-lg">
                    <div class="mx-auto">
                        <h1 class="text-3xl text-main tracking-tight text-gray-900">
                            {{ $record->distribution->title ?? 'Distribution Details' }}
                        </h1>

                        <!-- Distribution Details -->
                        <p class="text-gray-600 text-sm mt-1">
                            <span class="font-semibold">Date:</span> {{ $record->distribution_date ? $record->distribution_date->format('F d, Y') : 'N/A' }} |
                            <span class="font-semibold">Location:</span> {{ $record->location ?? 'N/A' }} |
                            <span class="font-semibold">Total Beneficiaries:</span> {{ $progressData['total'] ?? 0 }} |
                            <span class="font-semibold">Claimed:</span> {{ $progressData['claimed'] ?? 0 }} |
                            <span class="font-semibold">Remaining:</span> {{ $progressData['remaining'] ?? 0 }}
                        </p>
                    </div>
                    <div class="mt-2"></div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-3">
                        @php
                            $percentage = $progressData['total'] > 0 ? ($progressData['claimed'] / $progressData['total']) * 100 : 0;
                        @endphp
                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="text-xs text-gray-500 text-right mt-1">{{ number_format($percentage, 1) }}% claimed</div>
                </header> --}}

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
                    <!-- Beneficiaries Tab -->
                    <div x-show="tab === 'beneficiaries'" x-cloak>
                        <livewire:staff.beneficiary-list :distribution="$record->id" />
                    </div>

                    <!-- Transactions Tab -->
                    <div x-show="tab === 'transactions'" x-cloak>
                        <livewire:staff.transaction-list :distribution="$record->id" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-support-layout>
