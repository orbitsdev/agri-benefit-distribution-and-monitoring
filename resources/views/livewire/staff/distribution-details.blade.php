<x-support-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Distribution Details</h2>
                <a href="{{ route('staff.distribution.details',['distribution' => $record->id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Distributions
                </a>
            </div>

            <div x-data="{ tab: 'beneficiaries' }">
                <livewire:staff.distribution-progress :distribution="$record->id" />

                <div class="mt-8"></div>

                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200 overflow-x-auto">
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
                <div class="mt-6 overflow-x-auto">
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
