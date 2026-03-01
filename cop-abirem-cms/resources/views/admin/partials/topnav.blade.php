  <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">

    <!-- Left Section -->
    <div class="flex items-center gap-4">

        <!-- Mobile Menu Button -->
        <button 
            class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition"
            @click="sidebarOpen = !sidebarOpen"
        >
            <svg class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-500">
            @yield('breadcrumb')
        </nav>
    </div>


    <!-- Right Section -->
    <div class="flex items-center gap-6">

        <!-- User Dropdown -->
        <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">

            <!-- Trigger -->
            <button 
                @click="open = !open"
                class="flex items-center gap-3 p-2 rounded-xl hover:bg-gray-100 transition"
                :aria-expanded="open"
                type="button"
            >

                <!-- Avatar -->
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-semibold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <!-- Name + Role -->
                <div class="hidden sm:block text-left">
                    <div class="text-sm font-semibold text-gray-800">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ auth()->user()->role->name }}
                    </div>
                </div>

                <!-- Chevron -->
                <svg class="w-4 h-4 text-gray-400 transition-transform"
                     :class="{ 'rotate-180': open }"
                     fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>


            <!-- Dropdown Menu -->
            <div 
                x-show="open"
                x-transition
                @click.outside="open = false"
                class="absolute right-0 mt-3 w-56 bg-white border border-gray-200 rounded-xl shadow-lg py-2 z-50"
                style="display: none;"
            >

                <a href="{{ route('admin.profile.show') }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                    My Profile
                </a>

                <a href="{{ route('admin.settings.system') }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                    Settings
                </a>

                <a href="{{ route('admin.profile.password') }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                    Change Password
                </a>

                <div class="my-2 border-t border-gray-200"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                        Sign Out
                    </button>
                </form>

            </div>
        </div>

    </div>
</header>