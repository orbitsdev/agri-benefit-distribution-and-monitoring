@php
    $user = Auth::user();
    $barangay = $user->barangay ?? null;
@endphp

<div class="relative flex items-center justify-center min-h-screen bg-cover bg-center px-6 py-12"
     style="background-image: url('{{ $barangay ? $barangay->getImage() : asset('images/placeholder-image.jpg') }}');">

    <!-- 🔳 Dark Overlay for Better Readability -->
    <div class="absolute inset-0 bg-black bg-opacity-60"></div>

    <!-- 🔴 Logout Button (Top-Right) -->
    <div class="absolute top-4 right-4 z-20">
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="text-white bg-red-600 px-4 py-2 rounded-md shadow-md hover:bg-red-700 flex items-center gap-2">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>

        <!-- Hidden Logout Form -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>

    <!-- 🏠 Code Entry Form -->
    <div class="relative z-10 w-full max-w-sm bg-white bg-opacity-90 p-6 rounded-lg shadow-md backdrop-blur-md">
        <div class="text-center">
            Support Code

            <!-- 📌 Display Barangay Name -->
            @if($barangay)
                <h2 class="mt-4 text-xl font-bold text-indigo-700">{{ $barangay->name }}</h2>
            @endif

            <h2 class="mt-2 text-2xl font-bold tracking-tight text-gray-900">Enter Code</h2>
        </div>

        <!-- 🔢 Livewire Form -->
        <div class="mt-6 space-y-6">
            <div>
                <label for="code" class="block text-sm font-medium text-gray-900">Code</label>
                <div class="mt-2">
                    <input type="text" wire:model="code" id="code" required
                           class="block w-full rounded-md border-gray-300 px-3 py-2 text-gray-900 shadow-sm
                                  focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <!-- ❌ Show Validation Error -->
                @error('code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- ✅ Submit Button with Spinner -->
            <div class="relative">
                <button wire:click="submitCode"
                        class="w-full rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700 flex items-center justify-center">
                    <span wire:loading.remove wire:target="submitCode">Submit</span>
                    <span wire:loading wire:target="submitCode" class="flex items-center">
                        <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0116 0h-2a6 6 0 00-12 0H4z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
