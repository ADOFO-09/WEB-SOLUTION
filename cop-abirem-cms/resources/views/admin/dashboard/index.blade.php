@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Period Selector -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center space-x-2">
            <select name="period" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                <option value="week" {{ $period == 'week' ? 'selected' : '' }}>This Week</option>
                <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>This Quarter</option>
                <option value="year" {{ $period == 'year' ? 'selected' : '' }}>This Year</option>
            </select>
            <select name="year" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                @forelse($financialYears as $fy)
                    <option value="{{ $fy->start_date->year }}"
                        {{ $year == $fy->start_date->year ? 'selected' : '' }}>
                        {{ $fy->name }}{{ $fy->is_active ? ' ★' : '' }}
                    </option>
                @empty
                    <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                @endforelse
            </select>
        </form>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($quickStats as $stat)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-{{ $stat['color'] }}-100">
                    @if($stat['icon'] == 'users')
                    <svg class="w-6 h-6 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    @elseif($stat['icon'] == 'calendar')
                    <svg class="w-6 h-6 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    @elseif($stat['icon'] == 'currency')
                    <svg class="w-6 h-6 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else
                    <svg class="w-6 h-6 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    @endif
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ $stat['label'] }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stat['value'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Financial Summary -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Financial Summary</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">GH₵ {{ number_format($financeStats['tithes'], 2) }}</p>
                    <p class="text-sm text-gray-600">Tithes</p>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">GH₵ {{ number_format($financeStats['offerings'], 2) }}</p>
                    <p class="text-sm text-gray-600">Offerings</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <p class="text-2xl font-bold text-purple-600">GH₵ {{ number_format($financeStats['donations'], 2) }}</p>
                    <p class="text-sm text-gray-600">Donations</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-2xl font-bold text-red-600">GH₵ {{ number_format($financeStats['expenses_paid'], 2) }}</p>
                    <p class="text-sm text-gray-600">Expenses</p>
                </div>
            </div>
            <div class="border-t pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Net Income:</span>
                    <span class="text-2xl font-bold {{ $financeStats['net_income'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        GH₵ {{ number_format($financeStats['net_income'], 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Membership Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Membership</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Members</span>
                    <span class="text-xl font-bold text-gray-900">{{ number_format($memberStats['total']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Active</span>
                    <span class="text-xl font-bold text-green-600">{{ number_format($memberStats['active']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">New This Period</span>
                    <span class="text-xl font-bold text-blue-600">{{ number_format($memberStats['new_this_period']) }}</span>
                </div>
                <div class="pt-4 border-t">
                    <p class="text-sm text-gray-500 mb-2">Gender Distribution</p>
                    <div class="flex space-x-4">
                        <div class="flex-1 text-center">
                            <span class="block text-lg font-bold">{{ $memberStats['by_gender']['male'] ?? 0 }}</span>
                            <span class="text-xs text-gray-500">Male</span>
                        </div>
                        <div class="flex-1 text-center">
                            <span class="block text-lg font-bold">{{ $memberStats['by_gender']['female'] ?? 0 }}</span>
                            <span class="text-xs text-gray-500">Female</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Attendance Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Attendance</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Sessions This Period</span>
                    <span class="font-bold">{{ $attendanceStats['sessions_this_period'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Attendance</span>
                    <span class="font-bold">{{ number_format($attendanceStats['total_attendance']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Average per Service</span>
                    <span class="font-bold">{{ $attendanceStats['average_attendance'] }}</span>
                </div>
                @if($attendanceStats['last_sunday'])
                <div class="pt-3 border-t mt-3">
                    <p class="text-sm text-gray-500">Last Sunday ({{ $attendanceStats['last_sunday']['date'] }})</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $attendanceStats['last_sunday']['total'] }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Visitor Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Visitors</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Visitors</span>
                    <span class="font-bold">{{ $visitorStats['total'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">New This Period</span>
                    <span class="font-bold text-green-600">{{ $visitorStats['new_this_period'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Converted to Members</span>
                    <span class="font-bold text-blue-600">{{ $visitorStats['converted'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pending Follow-up</span>
                    <span class="font-bold text-yellow-600">{{ $visitorStats['pending_followup'] }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.members.create') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition">
                    <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-700">Add New Member</span>
                </a>
                <a href="{{ route('admin.tithes.create') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition">
                    <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-700">Record Tithe</span>
                </a>
                <a href="{{ route('admin.attendance.create') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition">
                    <span class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-700">Start Attendance Session</span>
                </a>
                <a href="{{ route('admin.sms.compose') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition">
                    <span class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-700">Send SMS</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Members -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Members</h3>
                <a href="{{ route('admin.members.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentMembers as $member)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 font-medium">
                            {{ strtoupper(substr($member->first_name, 0, 1) . substr($member->last_name, 0, 1)) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $member->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $member->member_id }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400">{{ $member->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <p class="text-gray-500 text-sm py-4 text-center">No recent members</p>
                @endforelse
            </div>
        </div>

        <!-- Pending Expenses -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Pending Expenses</h3>
                <a href="{{ route('admin.expenses.index', ['status' => 'pending']) }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($pendingExpenses as $expense)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ Str::limit($expense->description, 30) }}</p>
                        <p class="text-xs text-gray-500">{{ $expense->reference_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-red-600">GH₵ {{ number_format($expense->amount, 2) }}</p>
                        <a href="{{ route('admin.expenses.show', $expense) }}" class="text-xs text-indigo-600">Review</a>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-sm py-4 text-center">No pending expenses</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
