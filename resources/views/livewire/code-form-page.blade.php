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


    <div class="relative z-10 w-full max-w-sm bg-white bg-opacity-90 p-6 rounded-lg shadow-md backdrop-blur-md">
        <div class="text-center">
            <h1 class="text-lg font-semibold text-gray-800">Distribution Support</h1>

            @if($barangay)
                <h2 class="mt-2 text-xl font-bold text-primary-700 uppercase">{{ $barangay->name }}</h2>
            @endif

            <p class="mt-2 text-sm text-gray-600">
                Enter the unique code to proceed.
            </p>
        </div>

        <div class="mt-6 space-y-6">
            <div>
                <input type="text" wire:model="code" id="code" required
                       class="block w-full rounded-md border-gray-300 px-3 py-2 text-gray-900 shadow-sm
                              focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                       placeholder="Enter Code">

                @error('code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="relative">
                <button wire:click="submitCode"
                        class="w-full rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-700 flex items-center justify-center">
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

            @if(session()->has('error'))
                <div class="mt-4 text-sm text-red-600 text-center">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>


</div>
