{{-- <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100  relative"  style="background: url('{{ asset('images/bg4.png') }}') center/cover no-repeat;"> --}}
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-b from-[#2563eb] to-[#1e3a8a] relative">
        <div class="absolute inset-0 bg-gradient-to-b from-[#2563eb] to-[#1e3a8a]"></div>
        <div class="relative">
            {{ $logo }}
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg relative">
            {{ $slot }}
        </div>
    </div>
