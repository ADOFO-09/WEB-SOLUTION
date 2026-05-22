@extends('layouts.member')

@section('title', 'My Attendance')

@section('header')
<h1 class="text-xl font-bold text-gray-900">Attendance History</h1>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-gray-500 mb-1">This Month</div>
        <div class="text-2xl font-bold text-blue-600">{{ $stats['this_month'] }}</div>
        <div class="text-xs text-gray-400">services attended</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-gray-500 mb-1">Year Total ({{ $year }})</div>
        <div class="text-2xl font-bold text-gray-900">{{ $stats['total_year'] }}</div>
        <div class="text-xs text-gray-400">services attended</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-gray-500 mb-1">Total Services</div>
        <div class="text-2xl font-bold text-gray-900">{{ $stats['total_sessions'] }}</div>
        <div class="text-xs text-gray-400">held this year</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-gray-500 mb-1">Attendance Rate</div>
        <div class="text-2xl font-bold {{ $stats['attendance_rate'] >= 70 ? 'text-green-600' : ($stats['attendance_rate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
            {{ $stats['attendance_rate'] }}%
        </div>
        <div class="text-xs text-gray-400">of services attended</div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form action="{{ route('member.attendance.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
        <div class="flex items-center space-x-2">
            <label class="text-sm text-gray-600">Year:</label>
            <select name="year" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach($years as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center space-x-2">
            <label class="text-sm text-gray-600">Month:</label>
            <select name="month" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Months</option>
                @foreach($months as $num => $name)
                <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
            Filter
        </button>
        @if($month)
        <a href="{{ route('member.attendance.index', ['year' => $year]) }}" class="text-sm text-gray-500 hover:text-gray-700">Clear</a>
        @endif
    </form>
</div>

<!-- Monthly Summary Chart -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Attendance - {{ $year }}</h3>
    <div class="grid grid-cols-12 gap-2">
        @foreach($monthlySummary as $data)
        <div class="text-center">
            <div class="relative h-32 bg-gray-100 rounded-lg overflow-hidden mb-2">
                @php
                    $maxCount = max(array_column($monthlySummary, 'count')) ?: 1;
                    $height = ($data['count'] / $maxCount) * 100;
                @endphp
                <div class="absolute bottom-0 left-0 right-0 bg-blue-500 transition-all" style="height: {{ $height }}%"></div>
                <span class="absolute inset-0 flex items-center justify-center text-sm font-bold {{ $data['count'] > 0 ? 'text-white' : 'text-gray-400' }}">
                    {{ $data['count'] }}
                </span>
            </div>
            <a href="{{ route('member.attendance.index', ['year' => $year, 'month' => $data['month_num']]) }}" 
               class="text-xs {{ $month == $data['month_num'] ? 'font-bold text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                {{ $data['month'] }}
            </a>
        </div>
        @endforeach
    </div>
</div>

<!-- Attendance Records -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
            Attendance Records
            @if($month)
            - {{ $months[$month] }} {{ $year }}
            @endif
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($records as $record)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $record->session->service_date->format($dateFormat) }}</div>
                        <div class="text-xs text-gray-500">{{ $record->session->service_date->format('l') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $record->session->serviceType->name ?? 'Service' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format($timeFormat) : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($record->is_late)
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Late</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">On Time</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>No attendance records found for this period.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($records->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $records->links() }}
    </div>
    @endif
</div>
@endsection
