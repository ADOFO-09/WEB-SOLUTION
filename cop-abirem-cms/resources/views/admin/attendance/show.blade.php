@extends('layouts.admin')

@section('title', ($attendance->serviceType->name ?? 'Session') . ' — Attendance')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center">
        <a href="{{ route('admin.attendance.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $attendance->serviceType->name ?? 'Service' }}</h1>
            <p class="text-sm text-gray-500">{{ $attendance->formatted_date }}</p>
        </div>
    </div>
    <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
        @if($attendance->is_open)
        <form action="{{ route('admin.attendance.close', $attendance) }}" method="POST">
            @csrf
            <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                Close Session
            </button>
        </form>
        @else
        <form action="{{ route('admin.attendance.reopen', $attendance) }}" method="POST">
            @csrf
            <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                Reopen Session
            </button>
        </form>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ── Attendance Method Cards (session must be open) ─────────────────── --}}
    @if($attendance->is_open)
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Manual Entry --}}
        <a href="{{ route('admin.attendance.mark', $attendance) }}"
           class="group flex items-center gap-4 p-5 bg-white rounded-xl shadow border-2 border-transparent hover:border-indigo-400 hover:shadow-md transition-all">
            <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center group-hover:bg-indigo-100">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-900">Manual Entry</p>
                <p class="text-xs text-gray-500 mt-0.5">Mark by name or member ID</p>
            </div>
        </a>

        {{-- QR Code --}}
        <a href="{{ route('admin.attendance.scanner', $attendance) }}"
           class="group flex items-center gap-4 p-5 bg-white rounded-xl shadow border-2 border-transparent hover:border-blue-400 hover:shadow-md transition-all">
            <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center group-hover:bg-blue-100">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-900">QR Code Scan</p>
                <p class="text-xs text-gray-500 mt-0.5">Members scan personal QR codes</p>
            </div>
        </a>

        {{-- Biometric --}}
        <button id="btn-open-bio" onclick="openBiometricPanel()"
            class="group flex items-center gap-4 p-5 bg-white rounded-xl shadow border-2 border-transparent hover:border-purple-400 hover:shadow-md transition-all text-left">
            <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-purple-50 flex items-center justify-center group-hover:bg-purple-100">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-900">Biometric Scan</p>
                <p class="text-xs text-gray-500 mt-0.5" id="bio-card-sub">Fingerprint attendance</p>
            </div>
        </button>

    </div>
    @endif

    {{-- ── Embedded Biometric Scanner Panel ───────────────────────────────── --}}
    <div id="bio-panel" class="hidden rounded-xl overflow-hidden shadow-xl">
        <style>
            .bio-ring {
                width: 140px; height: 140px; border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                border: 3px solid #334155; transition: all 0.4s ease;
            }
            .bio-ring.scanning { border-color: #a855f7; animation: bio-pulse 1.2s ease-in-out infinite; }
            .bio-ring.success  { border-color: #10b981; background: rgba(16,185,129,.1); animation: none; }
            .bio-ring.error    { border-color: #ef4444; animation: bio-shake .4s ease; }
            @keyframes bio-pulse {
                0%,100% { box-shadow: 0 0 0 0 rgba(168,85,247,.4); }
                50%      { box-shadow: 0 0 0 16px rgba(168,85,247,0); }
            }
            @keyframes bio-shake {
                0%,100% { transform: translateX(0); }
                25%      { transform: translateX(-6px); }
                75%      { transform: translateX(6px); }
            }
            .bio-icon { color: #475569; transition: color .3s; }
            .bio-ring.scanning .bio-icon { color: #a855f7; }
            .bio-ring.success  .bio-icon { color: #10b981; }
            .bio-ring.error    .bio-icon { color: #ef4444; }
        </style>

        {{-- Header bar --}}
        <div class="bg-gray-900 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-gray-600 flex-shrink-0" id="bio-dot"></span>
                <span class="text-white font-semibold text-sm">Biometric Scanner</span>
                <span class="text-gray-400 text-sm" id="bio-status-text">Connecting to bridge…</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.attendance.biometric', $attendance) }}" target="_blank"
                   class="text-xs text-gray-400 hover:text-white border border-gray-700 rounded px-2 py-1 transition-colors">
                    Open full screen ↗
                </a>
                <button onclick="closeBiometricPanel()" class="text-gray-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="bg-gray-800 px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Fingerprint ring + prompt --}}
            <div class="flex flex-col items-center justify-center py-4 gap-4">
                <div class="bio-ring" id="bio-ring">
                    <svg class="bio-icon" width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
                            d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                    </svg>
                </div>
                <div class="text-center">
                    <p class="text-white font-semibold" id="bio-prompt">Initialising…</p>
                    <p class="text-gray-400 text-sm mt-0.5" id="bio-sub">&nbsp;</p>
                </div>

                {{-- Member result flash --}}
                <div id="bio-result" class="hidden w-full max-w-xs rounded-lg border bg-gray-700 border-gray-600 p-3 flex items-center gap-3">
                    <div id="bio-avatar"
                         class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center text-sm font-bold text-gray-300 flex-shrink-0 overflow-hidden"></div>
                    <div>
                        <p class="text-white text-sm font-semibold" id="bio-member-name"></p>
                        <p class="text-gray-400 text-xs" id="bio-member-meta"></p>
                    </div>
                </div>
            </div>

            {{-- Recent check-ins log --}}
            <div>
                <p class="text-gray-400 text-xs uppercase tracking-wide mb-3">Recent check-ins</p>
                <div id="bio-log" class="space-y-2 max-h-56 overflow-y-auto">
                    <p class="text-gray-500 text-sm" id="bio-log-empty">No check-ins yet this session.</p>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <p class="text-gray-400 text-xs">
                        <span class="text-white font-semibold text-lg" id="bio-count">0</span>
                        <span class="ml-1">biometric check-ins</span>
                    </p>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Main grid: sidebar + records ───────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left sidebar --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Session Info --}}
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Session Info</h3>
                    @if($attendance->is_open)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <span class="w-1.5 h-1.5 mr-1 bg-green-400 rounded-full animate-pulse"></span>Open
                    </span>
                    @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                        Closed
                    </span>
                    @endif
                </div>
                <dl class="space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Service Type</dt>
                        <dd class="font-medium text-gray-900">{{ $attendance->serviceType->name ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Date</dt>
                        <dd class="font-medium text-gray-900">{{ $attendance->service_date->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Time</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $attendance->start_time?->format('g:i A') }}
                            @if($attendance->end_time) – {{ $attendance->end_time->format('g:i A') }}@endif
                        </dd>
                    </div>
                    @if($attendance->ministry)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Ministry</dt>
                        <dd class="font-medium text-gray-900">{{ $attendance->ministry->name }}</dd>
                    </div>
                    @endif
                    @if($attendance->theme)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Theme</dt>
                        <dd class="font-medium text-gray-900">{{ $attendance->theme }}</dd>
                    </div>
                    @endif
                    @if($attendance->preacher)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Preacher</dt>
                        <dd class="font-medium text-gray-900">{{ $attendance->preacher }}</dd>
                    </div>
                    @endif
                </dl>
                @if($attendance->closed_at)
                <p class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-400">
                    Closed {{ $attendance->closed_at->format('M d, Y g:i A') }}
                    by {{ $attendance->closedBy->name ?? 'System' }}
                </p>
                @endif
            </div>

            {{-- Statistics --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Statistics</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="text-center p-3 bg-indigo-50 rounded-lg">
                        <div class="text-2xl font-bold text-indigo-600">{{ $stats['total'] }}</div>
                        <div class="text-xs text-indigo-600 mt-0.5">Total</div>
                    </div>
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['members'] }}</div>
                        <div class="text-xs text-blue-600 mt-0.5">Members</div>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['visitors'] }}</div>
                        <div class="text-xs text-green-600 mt-0.5">Visitors</div>
                    </div>
                    <div class="text-center p-3 bg-amber-50 rounded-lg">
                        <div class="text-2xl font-bold text-amber-600">{{ $stats['late'] }}</div>
                        <div class="text-xs text-amber-600 mt-0.5">Late</div>
                    </div>
                </div>
                @if($attendance->total_children)
                <div class="mt-3 text-center p-3 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ $attendance->total_children }}</div>
                    <div class="text-xs text-purple-600 mt-0.5">Children</div>
                </div>
                @endif
            </div>

            {{-- QR Code --}}
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-900">QR Code</h3>
                    @if($attendance->allow_qr_attendance)
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                    @else
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Disabled</span>
                    @endif
                </div>
                <p class="text-2xl font-bold text-blue-600 mb-3">
                    {{ $attendance->records()->where('attendance_method', 'qr_code')->count() }}
                    <span class="text-sm font-normal text-gray-500 ml-1">via QR</span>
                </p>
                <div class="space-y-2">
                    @if($attendance->is_open)
                    <a href="{{ route('admin.attendance.qr-display', $attendance) }}"
                       class="block w-full text-center px-3 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                        Display QR Code
                    </a>
                    @endif
                    <form action="{{ route('admin.attendance.toggle-qr', $attendance) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 text-sm font-medium rounded-lg border
                            {{ $attendance->allow_qr_attendance ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                            {{ $attendance->allow_qr_attendance ? 'Disable QR' : 'Enable QR' }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Biometric --}}
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-900">Biometric</h3>
                    @php $bioCount = $attendance->records()->where('attendance_method', 'biometric')->count(); @endphp
                    @if($bioCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ $bioCount }} scanned</span>
                    @endif
                </div>
                <p class="text-2xl font-bold text-purple-600 mb-3" id="sidebar-bio-count">
                    {{ $bioCount }}
                    <span class="text-sm font-normal text-gray-500 ml-1">via fingerprint</span>
                </p>
                <div class="space-y-2">
                    @if($attendance->is_open)
                    <button onclick="openBiometricPanel()"
                        class="block w-full text-center px-3 py-2 text-sm font-medium rounded-lg bg-purple-600 text-white hover:bg-purple-700">
                        <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                        </svg>
                        Start Biometric Scan
                    </button>
                    @else
                    <p class="text-xs text-gray-400 text-center">Session is closed</p>
                    @endif
                    <a href="{{ route('admin.members.index') }}?biometric=enrolled"
                       class="block w-full text-center px-3 py-2 text-sm font-medium rounded-lg border border-purple-200 text-purple-600 hover:bg-purple-50">
                        Manage Enrollment
                    </a>
                </div>
            </div>

        </div>

        {{-- Records table --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-base font-semibold text-gray-900">Attendance Records</h3>
                    <span class="text-sm text-gray-500" id="records-count">{{ $stats['total'] }} records</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="records-tbody">
                            @forelse($attendance->records as $record)
                            <tr>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            @if($record->member && $record->member->photo_path)
                                            <img class="h-8 w-8 rounded-full object-cover"
                                                 src="{{ asset('storage/' . $record->member->photo_path) }}" alt="">
                                            @else
                                            <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                                <span class="text-gray-500 text-xs font-medium">
                                                    {{ strtoupper(substr($record->attendee_name, 0, 2)) }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $record->attendee_name }}</p>
                                            @if($record->member)
                                            <p class="text-xs text-gray-400">{{ $record->member->member_id }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $record->member_id ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $record->member_id ? 'Member' : 'Visitor' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $record->check_in_time->format('g:i A') }}
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @php
                                    $method = $record->attendance_method;
                                    @endphp
                                    @if($method === 'biometric')
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-purple-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                                        </svg>Biometric
                                    </span>
                                    @elseif($method === 'qr_code')
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-blue-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>QR Code
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-600">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>Manual
                                    </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @if($record->is_late)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">Late</span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">On Time</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr id="empty-row">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                                    No attendance records for this session yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
const SESSION_ID   = {{ $attendance->id }};
const MEMBERS_URL  = "{{ route('admin.attendance.biometric.members', $attendance) }}";
const VERIFY_URL   = "{{ route('admin.attendance.biometric.verify') }}";
const CSRF_TOKEN   = document.querySelector('meta[name="csrf-token"]')?.content ?? "{{ csrf_token() }}";

let ws = null;
let scanning = false;
let bridgeConnected = false;
let enrolledMembers = [];
const checkedIn = new Set();
let bioCount = {{ $attendance->records()->where('attendance_method','biometric')->count() }};

// ── Panel open / close ─────────────────────────────────────────────────────
function openBiometricPanel() {
    document.getElementById('bio-panel').classList.remove('hidden');
    document.getElementById('bio-panel').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    if (!ws) initBiometric();
}

function closeBiometricPanel() {
    document.getElementById('bio-panel').classList.add('hidden');
    if (ws) { ws.close(); ws = null; }
    bridgeConnected = false;
}

// ── Biometric initialisation ───────────────────────────────────────────────
async function initBiometric() {
    setBioStatus('connecting');
    setPrompt('Connecting to scanner bridge…', '');

    // Fetch enrolled members for this session
    let data;
    try {
        const r = await fetch(MEMBERS_URL, { headers: { Accept: 'application/json' } });
        data = await r.json();
    } catch (_) {
        setBioStatus('error');
        setPrompt('Could not load member list', 'Check the server connection');
        return;
    }

    enrolledMembers = data.members ?? [];
    (data.checked_in_ids ?? []).forEach(id => checkedIn.add(id));

    if (enrolledMembers.length === 0) {
        setBioStatus('warning');
        setPrompt('No enrolled members', 'Enrol members via Members → Biometric');
        return;
    }

    // Connect to bridge
    connectWs();
}

function connectWs() {
    try {
        ws = new WebSocket('ws://localhost:15896/fingerprint');

        ws.onopen = () => {
            bridgeConnected = true;
            setBioStatus('connected');
            setPrompt('Place finger on the scanner', `${enrolledMembers.length} members loaded`);
            ws.send(JSON.stringify({
                action: 'start_identify',
                members: enrolledMembers.map(m => ({ id: m.id, t1: m.t1, t2: m.t2 || null }))
            }));
        };

        ws.onmessage = (e) => {
            let d;
            try { d = JSON.parse(e.data); } catch (_) { return; }

            if (d.type === 'ready') {
                setPrompt('Place finger on the scanner', `${d.count} templates loaded`);

            } else if (d.type === 'identify_result') {
                handleIdentifyResult(d);
            }
        };

        ws.onerror = () => {
            ws = null; bridgeConnected = false;
            setBioStatus('error');
            setPrompt('Scanner bridge not running',
                'Start run-bridge.bat as Administrator, then reopen this panel');
        };

        ws.onclose = () => {
            ws = null; bridgeConnected = false;
        };
    } catch (_) {
        setBioStatus('error');
        setPrompt('Scanner bridge not running', 'Start run-bridge.bat as Administrator');
    }
}

// ── Identify result ────────────────────────────────────────────────────────
async function handleIdentifyResult(d) {
    if (scanning) return;
    scanning = true;
    setRing('scanning');
    setPrompt('Matching…', '');

    await sleep(200);

    if (!d.matched) {
        setRing('error');
        setPrompt('Not recognised', 'Fingerprint not found — try again');
        showResult('error', null, 'Fingerprint not recognised', '');
        scanning = false;
        resetAfter(3000);
        return;
    }

    const member = enrolledMembers.find(m => m.id === d.member_id);
    if (!member) {
        setRing('error');
        setPrompt('Member not found', '');
        scanning = false;
        resetAfter(3000);
        return;
    }

    if (checkedIn.has(member.id)) {
        setRing('error');
        setPrompt('Already checked in', member.name);
        showResult('warning', member, member.name, 'Already checked in this session');
        scanning = false;
        resetAfter(3000);
        return;
    }

    // Record on server
    try {
        const res = await fetch(VERIFY_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, Accept: 'application/json' },
            body: JSON.stringify({ session_id: SESSION_ID, member_id: member.id }),
        });
        const json = await res.json();

        if (json.success) {
            checkedIn.add(member.id);
            bioCount++;
            document.getElementById('bio-count').textContent = bioCount;
            document.getElementById('sidebar-bio-count').innerHTML =
                bioCount + '<span class="text-sm font-normal text-gray-500 ml-1">via fingerprint</span>';
            setRing('success');
            setPrompt('Welcome!', json.check_in_time);
            showResult('success', member, member.name, 'Checked in · ' + json.check_in_time);
            addLog(member.name, json.check_in_time);
            addRecordRow(member, json.check_in_time);
        } else if (json.already_in) {
            checkedIn.add(member.id);
            setRing('error');
            setPrompt('Already checked in', member.name);
            showResult('warning', member, member.name, 'Already checked in this session');
        } else {
            setRing('error');
            setPrompt('Error', json.message || 'Could not record attendance');
        }
    } catch (_) {
        setRing('error');
        setPrompt('Network error', 'Check connection and try again');
    }

    scanning = false;
    resetAfter(3500);
}

// ── UI helpers ─────────────────────────────────────────────────────────────
function setBioStatus(state) {
    const dot  = document.getElementById('bio-dot');
    const text = document.getElementById('bio-status-text');
    const map = {
        connecting: ['bg-yellow-400', 'Connecting…'],
        connected:  ['bg-green-400',  'Bridge connected'],
        error:      ['bg-red-500',    'Bridge not connected'],
        warning:    ['bg-yellow-400', 'Warning'],
    };
    const [cls, label] = map[state] ?? ['bg-gray-500', state];
    dot.className  = `w-2.5 h-2.5 rounded-full flex-shrink-0 ${cls}`;
    text.textContent = label;

    // Update card subtitle
    const sub = document.getElementById('bio-card-sub');
    if (sub) sub.textContent = label;
}

function setRing(state) {
    document.getElementById('bio-ring').className = 'bio-ring ' + state;
}

function setPrompt(text, sub) {
    document.getElementById('bio-prompt').textContent = text;
    document.getElementById('bio-sub').textContent = sub || ' ';
}

function showResult(type, member, name, meta) {
    const el     = document.getElementById('bio-result');
    const avatar = document.getElementById('bio-avatar');
    el.classList.remove('hidden');
    document.getElementById('bio-member-name').textContent = name;
    document.getElementById('bio-member-meta').textContent = meta;
    const borderMap = { success: 'border-green-500', warning: 'border-amber-500', error: 'border-red-500' };
    el.className = `w-full max-w-xs rounded-lg border bg-gray-700 ${borderMap[type] ?? 'border-gray-600'} p-3 flex items-center gap-3`;
    if (member?.photo) {
        avatar.innerHTML = `<img src="${member.photo}" class="w-full h-full object-cover" alt="">`;
    } else {
        avatar.innerHTML = name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
    }
}

function addLog(name, time) {
    document.getElementById('bio-log-empty')?.remove();
    const log   = document.getElementById('bio-log');
    const entry = document.createElement('div');
    entry.className = 'flex items-center gap-2 py-1.5 border-b border-gray-700 last:border-0';
    entry.innerHTML = `
        <span class="w-2 h-2 rounded-full bg-green-400 flex-shrink-0"></span>
        <span class="flex-1 text-sm text-gray-300 truncate">${name}</span>
        <span class="text-xs text-gray-500">${time}</span>`;
    log.prepend(entry);
}

function addRecordRow(member, time) {
    // Remove empty-state row if present
    document.getElementById('empty-row')?.remove();

    const tbody = document.getElementById('records-tbody');
    const tr = document.createElement('tr');
    tr.className = 'bg-purple-50';
    const initials = member.name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
    const avatar = member.photo
        ? `<img class="h-8 w-8 rounded-full object-cover" src="${member.photo}" alt="">`
        : `<div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center"><span class="text-purple-600 text-xs font-medium">${initials}</span></div>`;

    tr.innerHTML = `
        <td class="px-5 py-3 whitespace-nowrap">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 h-8 w-8">${avatar}</div>
                <div><p class="text-sm font-medium text-gray-900">${member.name}</p></div>
            </div>
        </td>
        <td class="px-5 py-3 whitespace-nowrap">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Member</span>
        </td>
        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-500">${time}</td>
        <td class="px-5 py-3 whitespace-nowrap">
            <span class="inline-flex items-center gap-1 text-xs font-medium text-purple-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                </svg>Biometric
            </span>
        </td>
        <td class="px-5 py-3 whitespace-nowrap">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">On Time</span>
        </td>`;

    tbody.prepend(tr);

    // Update records count
    const total = tbody.querySelectorAll('tr').length;
    document.getElementById('records-count').textContent = total + ' records';
}

function resetAfter(ms) {
    setTimeout(() => {
        setRing('');
        document.getElementById('bio-result').classList.add('hidden');
        if (bridgeConnected) {
            setPrompt('Place finger on the scanner', '');
        }
    }, ms);
}

function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }
</script>
@endpush
