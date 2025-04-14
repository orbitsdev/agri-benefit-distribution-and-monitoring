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
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">General Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Performed At</p>
                <p class="text-gray-900 dark:text-gray-200">
                    {{ $record->performed_at ? \Carbon\Carbon::parse($record->performed_at)->format('F d, Y h:i A') : 'N/A' }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                <span class="px-3 py-1 text-white font-semibold rounded-lg mt-1 inline-block
                    {{ $record->action === 'Claimed' ? 'bg-green-600' : 'bg-gray-600' }}">
                    {{ $record->action }}
                </span>
            </div>
        </div>
    </div>

    <!-- Beneficiary Details -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Beneficiary Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                <p class="text-gray-900 dark:text-gray-200">
                    {{ trim(($record->beneficiary_details['first_name'] ?? '') . ' ' . 
                        ($record->beneficiary_details['middle_name'] ?? '') . ' ' . 
                        ($record->beneficiary_details['last_name'] ?? '') . ' ' . 
                        ($record->beneficiary_details['ext_name'] ?? '')) }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">RSBSA Number</p>
                <p class="text-gray-900 dark:text-gray-200">{{ $record->beneficiary_details['rsbsa_no'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Contact</p>
                <p class="text-gray-900 dark:text-gray-200">{{ $record->beneficiary_details['contact_num'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                <p class="text-gray-900 dark:text-gray-200">{{ $record->beneficiary_details['email'] ?? 'N/A' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                <p class="text-gray-900 dark:text-gray-200">
                    {{ $record->beneficiary_details['farmer_address'] ?? 'N/A' }}
                    {{ isset($record->beneficiary_details['farmer_address_mun']) ? ', ' . $record->beneficiary_details['farmer_address_mun'] : '' }}
                    {{ isset($record->beneficiary_details['farmer_address_prv']) ? ', ' . $record->beneficiary_details['farmer_address_prv'] : '' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Crop Details -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Crop Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Crop</p>
                <p class="text-gray-900 dark:text-gray-200">{{ $record->crops_details['crop_name'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">QR Code</p>
                <p class="text-gray-900 dark:text-gray-200">{{ $record->crops_details['unique_code'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Claim Status</p>
                <p class="text-gray-900 dark:text-gray-200">
                    {{ isset($record->crops_details['is_claimed']) && $record->crops_details['is_claimed'] ? 'Claimed' : 'Not Claimed' }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Date Claimed</p>
                <p class="text-gray-900 dark:text-gray-200">
                    {{ isset($record->crops_details['date_claimed']) && $record->crops_details['date_claimed'] 
                        ? \Carbon\Carbon::parse($record->crops_details['date_claimed'])->format('F d, Y h:i A') 
                        : 'N/A' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Distribution Details -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Distribution Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Distribution Title</p>
                <p class="text-gray-900 dark:text-gray-200">{{ $record->distribution_details['title'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Distribution Date</p>
                <p class="text-gray-900 dark:text-gray-200">
                    {{ isset($record->distribution_details['distribution_date']) 
                        ? \Carbon\Carbon::parse($record->distribution_details['distribution_date'])->format('F d, Y') 
                        : 'N/A' }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Barangay</p>
                <p class="text-gray-900 dark:text-gray-200">{{ $record->barangay_details['name'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Location/Venue</p>
                <p class="text-gray-900 dark:text-gray-200">{{ $record->barangay_distribution_details['location'] ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Recorder Details -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Recorded By</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                <p class="text-gray-900 dark:text-gray-200">{{ $record->recorder_details['name'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Role</p>
                <p class="text-gray-900 dark:text-gray-200">{{ $record->recorder_details['role'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                <p class="text-gray-900 dark:text-gray-200">{{ $record->recorder_details['email'] ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>
