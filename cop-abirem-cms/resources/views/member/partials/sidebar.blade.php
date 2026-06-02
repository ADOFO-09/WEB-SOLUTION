<style>
.m-nav-section-toggle {
    display: flex;
    width: 100%;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    box-sizing: border-box;
    background: rgba(255,255,255,0.04);
    border: none;
    border-left: 2px solid rgba(255,255,255,0.1);
    border-radius: 7px;
    color: rgba(255,255,255,0.5);
    font-size: 0.67rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 0.52rem 0.7rem 0.52rem 0.65rem;
    font-family: inherit;
    transition: background 0.18s ease, color 0.18s ease, border-color 0.18s ease;
    margin-bottom: 1px;
}
.m-nav-section-toggle:hover {
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.8);
    border-left-color: rgba(212,175,55,0.45);
}
.m-nav-section-toggle.section-open {
    background: rgba(212,175,55,0.1);
    color: rgba(212,175,55,0.9);
    border-left-color: rgba(212,175,55,0.75);
}
.m-nav-section-chevron {
    width: 13px;
    height: 13px;
    flex-shrink: 0;
    transition: transform 0.22s ease;
    opacity: 0.75;
}
.rotate-180 { transform: rotate(180deg); }
</style>

<aside class="sidebar" :class="{ 'open': sidebarOpen }">

    {{-- Header --}}
    <div class="sidebar-logo">
        <div style="display:flex;align-items:center;gap:0.75rem;">
            <div style="width:38px;height:38px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 8px rgba(59,130,246,0.4);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" style="width:20px;height:20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                </svg>
            </div>
            <div>
                <div style="color:#fff;font-weight:700;font-size:1rem;line-height:1.2;letter-spacing:-0.01em;">{{ \App\Models\Setting::get('church_name', 'COP Abirem') }}</div>
                <div style="color:rgba(255,255,255,0.5);font-size:0.675rem;text-transform:uppercase;letter-spacing:0.08em;margin-top:1px;">Member Portal</div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav" @click="if(window.innerWidth < 1024) sidebarOpen = false" style="padding-bottom:2rem;">

        {{-- Dashboard --}}
        <div class="nav-section">
            <a href="{{ route('member.dashboard') }}"
               class="nav-link {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                Dashboard
            </a>
        </div>

        {{-- My Profile --}}
        @php $profileOpen = request()->routeIs('member.profile.*'); @endphp
        <div class="nav-section" x-data="{ open: {{ $profileOpen ? 'true' : 'false' }} }">
            <button type="button"
                    class="m-nav-section-toggle"
                    :class="{ 'section-open': open }"
                    @click.stop="open = !open">
                My Profile
                <svg class="m-nav-section-chevron" :class="{ 'rotate-180': open }"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
            <div x-show="open" x-transition style="overflow:hidden;">
                <a href="{{ route('member.profile.show') }}"
                   class="nav-link {{ request()->routeIs('member.profile.show') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    View Profile
                </a>
                <a href="{{ route('member.profile.edit') }}"
                   class="nav-link {{ request()->routeIs('member.profile.edit') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                    </svg>
                    Edit Profile
                </a>
                <a href="{{ route('member.profile.password') }}"
                   class="nav-link {{ request()->routeIs('member.profile.password') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                    </svg>
                    Change Password
                </a>
            </div>
        </div>

        {{-- Attendance --}}
        @php $attendanceOpen = request()->routeIs('member.attendance.*'); @endphp
        <div class="nav-section" x-data="{ open: {{ $attendanceOpen ? 'true' : 'false' }} }">
            <button type="button"
                    class="m-nav-section-toggle"
                    :class="{ 'section-open': open }"
                    @click.stop="open = !open">
                Attendance
                <svg class="m-nav-section-chevron" :class="{ 'rotate-180': open }"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
            <div x-show="open" x-transition style="overflow:hidden;">
                <a href="{{ route('member.attendance.index') }}"
                   class="nav-link {{ request()->routeIs('member.attendance.index') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    My Attendance
                </a>
                <a href="{{ route('member.attendance.scan') }}"
                   class="nav-link {{ request()->routeIs('member.attendance.scan', 'member.attendance.verify', 'member.attendance.record') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                    </svg>
                    Scan QR Code
                </a>
            </div>
        </div>

        {{-- Giving --}}
        @php
            $givingOpen = request()->routeIs('member.giving.*')
                       || request()->routeIs('member.pledges.*')
                       || request()->routeIs('member.reports.*');
        @endphp
        <div class="nav-section" x-data="{ open: {{ $givingOpen ? 'true' : 'false' }} }">
            <button type="button"
                    class="m-nav-section-toggle"
                    :class="{ 'section-open': open }"
                    @click.stop="open = !open">
                Giving &amp; Pledges
                <svg class="m-nav-section-chevron" :class="{ 'rotate-180': open }"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
            <div x-show="open" x-transition style="overflow:hidden;">
                <a href="{{ route('member.giving.index') }}"
                   class="nav-link {{ request()->routeIs('member.giving.index') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5" />
                    </svg>
                    Overview
                </a>
                <a href="{{ route('member.giving.tithes') }}"
                   class="nav-link {{ request()->routeIs('member.giving.tithes') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tithes
                </a>
                <a href="{{ route('member.giving.offerings') }}"
                   class="nav-link {{ request()->routeIs('member.giving.offerings') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21" />
                    </svg>
                    Offerings
                </a>
                <a href="{{ route('member.giving.donations') }}"
                   class="nav-link {{ request()->routeIs('member.giving.donations') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                    Donations
                </a>
                <a href="{{ route('member.pledges.index') }}"
                   class="nav-link {{ request()->routeIs('member.pledges.*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Pledges
                </a>
                <a href="{{ route('member.giving.statement') }}"
                   class="nav-link {{ request()->routeIs('member.giving.statement*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Giving Statement
                </a>
                <a href="{{ route('member.reports.contributions') }}"
                   class="nav-link {{ request()->routeIs('member.reports.contributions') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                    </svg>
                    Contributions Report
                </a>
            </div>
        </div>

    </nav>

    {{-- Footer: user info + logout --}}
    <div style="padding:1rem;border-top:1px solid rgba(255,255,255,0.08);background:rgba(0,0,0,0.15);flex-shrink:0;">
        @php $member = auth()->user()->member; @endphp
        @if($member)
        <div style="display:flex;align-items:center;gap:0.625rem;margin-bottom:0.75rem;">
            @if($member->photo_path)
            <img src="{{ asset('storage/' . $member->photo_path) }}"
                 style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.15);" alt="">
            @else
            <div style="width:36px;height:36px;border-radius:50%;background:rgba(59,130,246,0.4);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem;flex-shrink:0;border:2px solid rgba(255,255,255,0.15);">
                {{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}
            </div>
            @endif
            <div style="flex:1;min-width:0;">
                <div style="color:#fff;font-size:0.8rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $member->full_name }}</div>
                <div style="color:rgba(255,255,255,0.45);font-size:0.7rem;">{{ $member->member_id }}</div>
            </div>
        </div>
        @endif
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    style="display:flex;align-items:center;gap:0.5rem;width:100%;padding:0.45rem 0.75rem;border-radius:7px;background:rgba(239,68,68,0.12);color:rgba(255,120,120,0.9);border:1px solid rgba(239,68,68,0.2);font-size:0.78rem;font-weight:600;cursor:pointer;transition:background 0.2s;"
                    onmouseover="this.style.background='rgba(239,68,68,0.22)'"
                    onmouseout="this.style.background='rgba(239,68,68,0.12)'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px;flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
                Sign Out
            </button>
        </form>
    </div>

</aside>
