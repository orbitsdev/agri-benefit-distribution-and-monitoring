<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md text-center">
        <h2 class="text-2xl font-semibold text-red-600">Access Denied</h2>
        <p class="text-gray-700 mt-2">
            You are not authorized to access this section.
        </p>

        <div class="mt-6">
            <button
                wire:click="removeCode"
                class="w-full bg-red-600 text-white py-2 rounded-lg shadow-md hover:bg-red-700">
                Remove Access & Return to Login
            </button>
        </div>
    </div>
</div>
