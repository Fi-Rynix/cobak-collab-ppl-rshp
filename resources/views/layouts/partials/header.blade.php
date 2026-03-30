<header class="h-20 bg-gradient-to-r from-blue-500 to-blue-400 border-b border-blue-600 flex items-center justify-between px-6 shadow-md flex-shrink-0"
    x-data="{ profileOpen: false }">

    <div class="flex items-center gap-4">
        {{-- Toggle for mobile --}}
        <button class="md:hidden text-white text-2xl" @click="sidebarOpen = !sidebarOpen">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <h1 class="text-xl font-bold text-white">
            @yield('title')
        </h1>
    </div>

    <div class="flex items-center gap-6 relative">
        {{-- User Name and Profile --}}
        <div class="flex items-center gap-3">
            {{-- User Name --}}
            <span class="text-white font-medium">{{ session('nama') ?? 'User' }}</span>

            {{-- Profile Dropdown --}}
            <div x-data="{ profileOpen: false }" class="relative">
                <button
                    @click="profileOpen = !profileOpen"
                    :class="profileOpen ? 'bg-blue-100 shadow-lg' : 'bg-white hover:bg-blue-50'"
                    class="w-10 h-10 rounded-full flex items-center justify-center shadow-md transition-all duration-200 focus:outline-none">
                    <svg :class="profileOpen ? 'text-blue-600' : 'text-blue-500 group-hover:text-blue-600'" class="w-6 h-6 transition-colors duration-200" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                    </svg>
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="profileOpen"
                    @click.outside="profileOpen = false"
                    x-transition
                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm text-gray-600">User</p>
                        <p class="font-semibold text-gray-800">{{ session('nama') ?? 'User' }}</p>
                    </div>
                    
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors duration-200 w-full text-left"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>

                </div>
            </div>
        </div>
    </div>

</header>
