<div class="p-6 space-y-6 bg-gray-100">
    <!-- Image Section -->
    <div class="flex justify-center">
        <a href="{{ $record->getImage() }}" target="_blank">
            <img src="{{ $record->getImage() }}" alt="Transaction Image"
                class="w-full max-w-sm rounded-lg shadow-md bg-gray-200 dark:bg-gray-700 object-cover aspect-[4/3]">
        </a>
    </div>

    <!-- Transaction Details -->
    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 text-center">Transaction Details</h2>

    <!-- General Information -->
    <div class=" dark:bg-gray-800 p-4 rounded-lg shadow-sm">
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">General Information</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Performed At</p>
        <p class="text-gray-900 dark:text-gray-200">
            {{ $record->performed_at ? \Carbon\Carbon::parse($record->performed_at)->format('F d, Y h:i A') : 'N/A' }}
        </p>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Status</p>
        <span class="px-3 py-1 font-semibold  rounded-lg mt-1 inline-block
            {{ $record->action === 'Claimed' ? 'bg-green-600' : 'bg-red-600' }}">
            {{ $record->action }}
        </span>
    </div>

    <!-- Beneficiary Details -->
    <div class=" dark:bg-gray-800 p-4 rounded-lg shadow-sm">
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Beneficiary Information</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Name</p>
        <p class="text-gray-900 dark:text-gray-200">{{ $record->beneficiary_details['name'] ?? 'N/A' }}</p>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Contact</p>
        <p class="text-gray-900 dark:text-gray-200">{{ $record->beneficiary_details['contact'] ?? 'N/A' }}</p>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Email</p>
        <p class="text-gray-900 dark:text-gray-200">{{ $record->beneficiary_details['email'] ?? 'N/A' }}</p>
    </div>

    <!-- Item & Distribution Details -->
    <div class=" dark:bg-gray-800 p-4 rounded-lg shadow-sm">
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Item & Distribution Details</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Item</p>
        <p class="text-gray-900 dark:text-gray-200">{{ $record->distribution_item_details['name'] ?? 'N/A' }}</p>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Quantity</p>
        <p class="text-gray-900 dark:text-gray-200">{{ $record->distribution_item_details['quantity'] ?? 'N/A' }}</p>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Distribution</p>
        <p class="text-gray-900 dark:text-gray-200">{{ $record->distribution_details['title'] ?? 'N/A' }}</p>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Distribution Code</p>
        <p class="text-gray-900 dark:text-gray-200">{{ $record->distribution_details['code'] ?? 'N/A' }}</p>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Distribution Date</p>
        <p class="text-gray-900 dark:text-gray-200">
            {{ isset($record->distribution_details['date']) ? \Carbon\Carbon::parse($record->distribution_details['date'])->format('F d, Y') : 'N/A' }}
        </p>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Location</p>
        <p class="text-gray-900 dark:text-gray-200">{{ $record->distribution_details['location'] ?? 'N/A' }}</p>
    </div>

    <!-- Barangay Details -->
    <div class=" dark:bg-gray-800 p-4 rounded-lg shadow-sm">
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Barangay Details</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Barangay</p>
        <p class="text-gray-900 dark:text-gray-200">{{ $record->barangay_details['name'] ?? 'N/A' }}</p>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Location</p>
        <p class="text-gray-900 dark:text-gray-200">{{ $record->barangay_details['location'] ?? 'N/A' }}</p>
    </div>

    <!-- Recorder Details -->
    <div class=" dark:bg-gray-800 p-4 rounded-lg shadow-sm">
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Recorded By</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Name</p>
        <p class="text-gray-900 dark:text-gray-200">{{ $record->recorder_details['name'] ?? 'N/A' }}</p>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Role</p>
        <p class="text-gray-900 dark:text-gray-200">{{ $record->recorder_details['role'] ?? 'N/A' }}</p>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Support Code</p>
        <p class="text-gray-900 dark:text-gray-200">{{ $record->recorder_details['unique_code'] ?? 'N/A' }}</p>
    </div>

    <!-- Back Button -->
    <div class="mt-6 text-center">
        <button type="button" onclick="window.history.back()"
            class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
            Back
        </button>
    </div>
</div>
