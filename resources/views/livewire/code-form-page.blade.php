@php
    $user = Auth::user();
    $barangay = $user->barangay ?? null;
@endphp

<div class="flex items-center justify-center min-h-screen bg-cover bg-center px-6 py-12"
     style="background-image: url('{{ $barangay ? $barangay->getImage() : asset('images/placeholder-image.jpg') }}');">
    <div class="w-full max-w-sm bg-white bg-opacity-80 p-6 rounded-lg shadow-md backdrop-blur-md">
        <div class="text-center">
            <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/plus/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company">
            
            <!-- Display Barangay Name -->
            @if($barangay)
                <h2 class="mt-4 text-xl font-bold text-indigo-700">{{ $barangay->name }}</h2>
            @endif

            <h2 class="mt-2 text-2xl font-bold tracking-tight text-gray-900">Enter Code</h2>
        </div>

        <!-- Livewire Form -->
        <div class="mt-6 space-y-6">
            <div>
                <label for="code" class="block text-sm font-medium text-gray-900">Code</label>
                <div class="mt-2">
                    <input type="text" wire:model.live="code" id="code" required class="block w-full rounded-md border-gray-300 px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <div>
                <button wire:click="submitCode" class="w-full rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white">
                    Submit
                </button>
                
            </div>
        </div>
    </div>
</div>
