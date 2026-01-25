<header class="top-nav">
    <div style="display: flex; align-items: center;">
        <button class="mobile-menu-btn" style="margin-right: 1rem; padding: 0.5rem; border-radius: 0.5rem; border: none; background: none; cursor: pointer;" @click="sidebarOpen = !sidebarOpen">
            <svg style="width: 1.5rem; height: 1.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
        <nav style="font-size: 0.875rem; color: #64748b;">
            @yield('breadcrumb')
        </nav>
    </div>

    <div style="display: flex; align-items: center; gap: 1rem;">
        <!-- User Dropdown -->
        <div class="dropdown" x-data="{ open: false }">
            <button @click="open = !open" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; border-radius: 0.5rem; border: none; background: none; cursor: pointer;">
                <div style="width: 2rem; height: 2rem; border-radius: 9999px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 600; font-size: 0.875rem;">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div style="text-align: left;">
                    <div style="font-size: 0.875rem; font-weight: 600; color: #374151;">{{ auth()->user()->name }}</div>
                    <div style="font-size: 0.75rem; color: #64748b;">{{ auth()->user()->role->name }}</div>
                </div>
                <svg style="width: 1rem; height: 1rem; color: #9ca3af;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            <div class="dropdown-menu" :class="{ 'show': open }" @click.outside="open = false">
                <a href="{{ route('admin.profile.show') }}" class="dropdown-item">
                    <svg style="width: 1rem; height: 1rem; display: inline; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    My Profile
                </a>
                <a href="{{ route('admin.profile.password') }}" class="dropdown-item">
                    <svg style="width: 1rem; height: 1rem; display: inline; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    Change Password
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item" style="width: 100%; text-align: left; color: #dc2626; border: none; background: none; cursor: pointer;">
                        <svg style="width: 1rem; height: 1rem; display: inline; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
