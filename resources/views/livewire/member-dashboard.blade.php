<div class="min-h-full">
    <nav class="bg-white">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <div class="flex items-center">
            <!-- Left section (empty for now) -->
          </div>

          <div class="hidden md:block">
            <div class="ml-4 flex items-center md:ml-6">
              <!-- Profile Dropdown -->
              <div class="relative ml-3" x-data="{ open: false }">

                <div class="flex items-center space-x-3">
                  <!-- Authenticated User's Name -->
                  <span class="text-gray-800 text-sm font-medium">
                    {{ Auth::user()->name }}
                  </span>

                  <button @click="open = !open" type="button" class="relative flex max-w-xs items-center rounded-full bg-primary-600 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600">
                    <span class="absolute -inset-1.5"></span>
                    <span class="sr-only">Open user menu</span>
                    <img class="size-8 rounded-full" src="{{ Auth::user()->getImage() }}" alt="{{ Auth::user()->name }}">
                  </button>
                </div>

                <!-- Dropdown menu -->
                <div x-show="open" @click.away="open = false" x-transition
                  class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none">
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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

    <main class="">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @livewire('distributions.distribution-beneficiaries-list')
      </div>
    </main>
  </div>

