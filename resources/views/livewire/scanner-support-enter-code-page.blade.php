<div class="flex items-center justify-center min-h-screen bg-gray-100 px-4">
    <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-md">
        <div class="text-center">
            <h2 class="text-xl font-semibold text-gray-700">Enter Support Code</h2>
        </div>
        <div class="mt-4">
            <input
                type="text"
                wire:model="code"
                placeholder="Enter code"
                class="w-full rounded-md border border-gray-300 p-3 text-gray-900 focus:outline-none focus:ring focus:ring-indigo-500"
            />
            @error('code')
            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="mt-4">
            <button
                wire:click="submitCode"
                class="w-full bg-indigo-600 text-white py-2 rounded-md shadow-md hover:bg-indigo-700"
            >
                Submit Code
            </button>
        </div>
    </div>
</div>
