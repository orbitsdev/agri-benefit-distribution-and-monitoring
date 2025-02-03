<header class="relative bg-white">
    <div class="h-1 bg-primary-600"></div>
    <nav aria-label="Top" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="">
            <div class="flex h-16 items-center">

                <div class="ml-auto flex items-center">
                    <!-- Profile Dropdown -->
                    <div class="ml-4 relative" x-data="{ open: false }">
                        <!-- Profile Button -->
                        <button
                            @click="open = ! open"
                            class="flex items-center text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                            title="Update Profile">
                            <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->getImage() }}" alt="{{ Auth::user()->name }}">
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            x-show="open"
                            @click.away="open = false"
                            class="absolute right-0 z-50 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                        >
                            <div class="py-1">
                                <!-- Logout Button -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        title="Log Out of Your Account">
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </nav>
</header>
