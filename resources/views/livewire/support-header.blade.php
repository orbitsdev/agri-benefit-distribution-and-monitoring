<nav class="bg-white shadow" x-data="{ menuOpen: false }">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <!-- Logo Section -->
            <div class="flex items-center">
                <a href="#" class="text-lg font-semibold text-gray-800">
                    Scanner App
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex md:items-center md:space-x-4">
                @can('view-member-dashboard')
                    <a href="{{ route('member.dashboard') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Member Dashboard
                    </a>
                @endcan
                @can('view-qr-scanner')
                    <a href="{{ route('qr-scan') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        QR Scanner
                    </a>
                @endcan

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="relative flex items-center rounded-full bg-primary-600 text-sm text-white focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2">
                        <span class="sr-only">Open user menu</span>
                        <img class="h-8 w-8 rounded-full" src="{{ Auth::user()->getImage() }}" alt="{{ Auth::user()->name }}">
                    </button>

                    <!-- Dropdown menu -->
                    <div x-show="open" @click.away="open = false" x-transition
                         class="absolute right-0 mt-2 w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5">

                        <!-- Show Exit Support Mode Only If User Has a Support Code -->
                        @if(Auth::user()->code)
                            <form method="POST" action="{{ route('support.logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    {{ __('Exit Support Mode') }}
                                </button>
                            </form>
                        @endif

                        <!-- Regular Logout Button -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="-mr-2 flex md:hidden">
                <button @click="menuOpen = !menuOpen"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <span class="sr-only">Open main menu</span>
                    <svg x-show="!menuOpen" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="menuOpen" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="menuOpen" @click.away="menuOpen = false" x-transition class="md:hidden bg-white shadow-lg border-t border-gray-200">
        <div class="px-4 py-3">
            <div>
                <!-- Member Dashboard Link -->
                @can('view-member-dashboard')
                    <a href="{{ route('member.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        Member Dashboard
                    </a>
                @endcan

                <!-- QR Scanner Link -->
                @can('view-qr-scanner')
                    <a href="{{ route('qr-scan') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        QR Scanner
                    </a>
                @endcan
            </div>

            <!-- Show Exit Support Mode Only If User Has a Support Code -->
            @if(Auth::user()->code)
                <form method="POST" action="{{ route('support.logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                        {{ __('Exit Support Mode') }}
                    </button>
                </form>
            @endif

            <!-- Regular Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</nav>
