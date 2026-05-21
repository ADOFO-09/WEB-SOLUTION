@extends('layouts.admin')

@section('title', 'Home')

@section('content')
{{-- Welcome Banner --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Welcome, {{ $user->name }}</h1>
        <p class="text-sm text-gray-500 mt-1">
            {{ $user->role->name ?? 'Staff' }} &mdash; {{ $now->format('l, F j, Y') }}
        </p>
    </div>
    <div class="hidden sm:flex items-center space-x-3">
        <a href="{{ route('admin.profile.show') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            My Profile
        </a>
    </div>
</div>

{{-- Stats Grid — only sections the user can access --}}
@php
    $hasAnyStats = isset($memberStats) || isset($visitorStats) || isset($attendanceStats)
                || isset($tithesStats) || isset($offeringsStats) || isset($donationsStats)
                || isset($expenseStats);
@endphp

@if($hasAnyStats)
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-6">

    {{-- Members --}}
    @isset($memberStats)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Members</span>
            <span class="w-9 h-9 rounded-full flex items-center justify-center bg-blue-50">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </span>
        </div>
        <div class="text-3xl font-bold text-gray-900">{{ number_format($memberStats['total']) }}</div>
        <div class="text-sm text-gray-500 mt-1">{{ number_format($memberStats['active']) }} active</div>
        <div class="mt-3 flex items-center text-xs text-green-600 font-medium">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            {{ $memberStats['new_this_month'] }} new this month
        </div>
        <a href="{{ route('admin.members.index') }}" class="mt-4 block text-xs text-blue-600 hover:underline">View all members &rarr;</a>
    </div>
    @endisset

    {{-- Visitors --}}
    @isset($visitorStats)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Visitors</span>
            <span class="w-9 h-9 rounded-full flex items-center justify-center bg-amber-50">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </span>
        </div>
        <div class="text-3xl font-bold text-gray-900">{{ number_format($visitorStats['total']) }}</div>
        <div class="text-sm text-gray-500 mt-1">{{ $visitorStats['new_this_month'] }} new this month</div>
        <div class="mt-3 flex items-center text-xs text-amber-600 font-medium">
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></path></svg>
            {{ $visitorStats['pending_followup'] }} pending follow-up
        </div>
        <a href="{{ route('admin.visitors.index') }}" class="mt-4 block text-xs text-amber-600 hover:underline">View all visitors &rarr;</a>
    </div>
    @endisset

    {{-- Attendance --}}
    @isset($attendanceStats)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Attendance</span>
            <span class="w-9 h-9 rounded-full flex items-center justify-center bg-violet-50">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </span>
        </div>
        <div class="text-3xl font-bold text-gray-900">{{ $attendanceStats['sessions_this_month'] }}</div>
        <div class="text-sm text-gray-500 mt-1">Sessions this month</div>
        @if($attendanceStats['last_session'])
        <div class="mt-3 text-xs text-gray-500">
            Last: {{ $attendanceStats['last_session']->service_date->format('M d, Y') }}
        </div>
        @endif
        <a href="{{ route('admin.attendance.index') }}" class="mt-4 block text-xs text-violet-600 hover:underline">View attendance &rarr;</a>
    </div>
    @endisset

    {{-- Tithes --}}
    @isset($tithesStats)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tithes</span>
            <span class="w-9 h-9 rounded-full flex items-center justify-center bg-emerald-50">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
        </div>
        <div class="text-2xl font-bold text-gray-900">GH₵ {{ number_format($tithesStats['this_month'], 2) }}</div>
        <div class="text-sm text-gray-500 mt-1">This month &bull; {{ $tithesStats['count'] }} payments</div>
        <a href="{{ route('admin.tithes.index') }}" class="mt-4 block text-xs text-emerald-600 hover:underline">View tithes &rarr;</a>
    </div>
    @endisset

    {{-- Offerings --}}
    @isset($offeringsStats)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Offerings</span>
            <span class="w-9 h-9 rounded-full flex items-center justify-center bg-teal-50">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </span>
        </div>
        <div class="text-2xl font-bold text-gray-900">GH₵ {{ number_format($offeringsStats['this_month'], 2) }}</div>
        <div class="text-sm text-gray-500 mt-1">This month &bull; {{ $offeringsStats['count'] }} entries</div>
        <a href="{{ route('admin.offerings.index') }}" class="mt-4 block text-xs text-teal-600 hover:underline">View offerings &rarr;</a>
    </div>
    @endisset

    {{-- Donations --}}
    @isset($donationsStats)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Donations</span>
            <span class="w-9 h-9 rounded-full flex items-center justify-center bg-cyan-50">
                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </span>
        </div>
        <div class="text-2xl font-bold text-gray-900">GH₵ {{ number_format($donationsStats['this_month'], 2) }}</div>
        <div class="text-sm text-gray-500 mt-1">This month &bull; {{ $donationsStats['count'] }} donations</div>
        <a href="{{ route('admin.donations.index') }}" class="mt-4 block text-xs text-cyan-600 hover:underline">View donations &rarr;</a>
    </div>
    @endisset

    {{-- Expenses --}}
    @isset($expenseStats)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Expenses</span>
            <span class="w-9 h-9 rounded-full flex items-center justify-center bg-red-50">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
            </span>
        </div>
        <div class="text-2xl font-bold text-gray-900">GH₵ {{ number_format($expenseStats['this_month'], 2) }}</div>
        <div class="text-sm text-gray-500 mt-1">Paid this month</div>
        @if($expenseStats['pending'] > 0)
        <div class="mt-3 flex items-center text-xs text-red-500 font-medium">
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            {{ $expenseStats['pending'] }} awaiting approval
        </div>
        @endif
        <a href="{{ route('admin.expenses.index') }}" class="mt-4 block text-xs text-red-500 hover:underline">View expenses &rarr;</a>
    </div>
    @endisset

</div>
@endif

{{-- Quick Actions --}}
@php
    $actions = [];

    if(auth()->user()->hasPermission('members.create'))
        $actions[] = ['label' => 'Add Member',       'route' => route('admin.members.create'),     'color' => 'blue',    'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'];
    if(auth()->user()->hasPermission('attendance.create'))
        $actions[] = ['label' => 'New Session',      'route' => route('admin.attendance.create'),  'color' => 'violet',  'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'];
    if(auth()->user()->hasPermission('visitors.create'))
        $actions[] = ['label' => 'Register Visitor', 'route' => route('admin.visitors.create'),    'color' => 'amber',   'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'];
    if(auth()->user()->hasPermission('tithes.create'))
        $actions[] = ['label' => 'Record Tithe',     'route' => route('admin.tithes.create'),      'color' => 'emerald', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'];
    if(auth()->user()->hasPermission('offerings.create'))
        $actions[] = ['label' => 'Record Offering',  'route' => route('admin.offerings.create'),   'color' => 'teal',    'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'];
    if(auth()->user()->hasPermission('expenses.create'))
        $actions[] = ['label' => 'Log Expense',      'route' => route('admin.expenses.create'),    'color' => 'red',     'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'];
    if(auth()->user()->hasPermission('sms.send'))
        $actions[] = ['label' => 'Send SMS',         'route' => route('admin.sms.compose'),        'color' => 'indigo',  'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'];
    if(auth()->user()->hasPermission('reports.view'))
        $actions[] = ['label' => 'View Reports',     'route' => route('admin.reports.index'),      'color' => 'gray',    'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'];
@endphp

@if(count($actions))
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Quick Actions</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
        @foreach($actions as $action)
        @php
            $colors = [
                'blue'    => ['bg' => 'bg-blue-50',    'text' => 'text-blue-700',    'hover' => 'hover:bg-blue-100'],
                'violet'  => ['bg' => 'bg-violet-50',  'text' => 'text-violet-700',  'hover' => 'hover:bg-violet-100'],
                'amber'   => ['bg' => 'bg-amber-50',   'text' => 'text-amber-700',   'hover' => 'hover:bg-amber-100'],
                'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'hover' => 'hover:bg-emerald-100'],
                'teal'    => ['bg' => 'bg-teal-50',    'text' => 'text-teal-700',    'hover' => 'hover:bg-teal-100'],
                'red'     => ['bg' => 'bg-red-50',     'text' => 'text-red-700',     'hover' => 'hover:bg-red-100'],
                'indigo'  => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-700',  'hover' => 'hover:bg-indigo-100'],
                'gray'    => ['bg' => 'bg-gray-50',    'text' => 'text-gray-700',    'hover' => 'hover:bg-gray-100'],
            ];
            $c = $colors[$action['color']] ?? $colors['gray'];
        @endphp
        <a href="{{ $action['route'] }}"
           class="flex flex-col items-center justify-center p-4 rounded-lg {{ $c['bg'] }} {{ $c['hover'] }} transition-colors text-center group">
            <svg class="w-6 h-6 {{ $c['text'] }} mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/>
            </svg>
            <span class="text-xs font-medium {{ $c['text'] }}">{{ $action['label'] }}</span>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- No permissions at all --}}
@if(!$hasAnyStats && !count($actions ?? []))
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
    </svg>
    <h3 class="text-lg font-semibold text-gray-700 mb-1">No permissions assigned yet</h3>
    <p class="text-sm text-gray-500">Contact your administrator to have permissions assigned to your account.</p>
</div>
@endif

{{-- My Account --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">My Account</h2>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg"
                 style="background-color: #1e3a5f;">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $user->role->name ?? 'No role' }}</p>
            </div>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.profile.edit') }}"
               class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Edit Profile
            </a>
            <a href="{{ route('admin.profile.password') }}"
               class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Change Password
            </a>
        </div>
    </div>
</div>
@endsection
