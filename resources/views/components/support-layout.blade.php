<div class="min-h-screen flex flex-col bg-gray-100">
    <!-- Livewire Support Header (Fixed for better UX) -->
    <div class="sticky top-0 z-50 w-full bg-white shadow-md">
        @livewire('support-header')
    </div>

    <!-- Main Content Wrapper -->
    <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-7xl">
            {{ $slot }}
        </div>
    </main>
</div>
