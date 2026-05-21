@extends('layouts.admin')

@section('title', 'Attendance Dashboard')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Attendance Dashboard</h1>
        <form action="{{ route('admin.dashboard.attendance') }}" method="GET" class="flex items-center space-x-2 mt-4 sm:mt-0">
            <select name="year" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                @foreach(range(date('Y'), date('Y') - 5) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Year Summary Cards -->
    @php
        $totalSessions   = collect($monthlyAttendance)->sum('sessions');
        $totalMembers    = collect($monthlyAttendance)->sum('members');
        $totalVisitors   = collect($monthlyAttendance)->sum('visitors');
        $avgMembersPerSession = $totalSessions > 0 ? round($totalMembers / $totalSessions) : 0;
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Sessions</div>
            <div class="text-2xl font-bold text-indigo-600">{{ number_format($totalSessions) }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $year }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Members Attended</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($totalMembers) }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $year }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Visitors</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($totalVisitors) }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $year }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Avg. Members/Session</div>
            <div class="text-2xl font-bold text-purple-600">{{ number_format($avgMembersPerSession) }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $year }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sessions by Service Type -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Sessions by Service Type — {{ $year }}</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($sessionsByType as $row)
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-900">{{ $row->serviceType?->name ?? 'Unknown' }}</span>
                        <span class="text-sm text-gray-500">{{ $row->sessions }} sessions</span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-400">
                        <span>Total attendance: {{ number_format($row->total_attendance) }}</span>
                        @if($row->sessions > 0)
                        <span>Avg: {{ number_format($row->total_attendance / $row->sessions) }}/session</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-400 text-sm">No sessions recorded for {{ $year }}</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Sessions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Recent Sessions</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentSessions as $session)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $session->serviceType?->name ?? 'Service' }}</div>
                        <div class="text-xs text-gray-400">{{ $session->service_date->format('D, d M Y') }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold text-gray-900">{{ number_format($session->total_members + $session->total_visitors) }}</div>
                        <div class="text-xs text-gray-400">{{ $session->total_members }}M / {{ $session->total_visitors }}V</div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-400 text-sm">No sessions found</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Monthly Attendance Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Monthly Attendance — {{ $year }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sessions</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Visitors</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg/Session</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($monthlyAttendance as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $row['month'] }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-700">{{ $row['sessions'] }}</td>
                        <td class="px-6 py-4 text-sm text-right text-green-600">{{ number_format($row['members']) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-blue-600">{{ number_format($row['visitors']) }}</td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">{{ number_format($row['members'] + $row['visitors']) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-500">
                            {{ $row['sessions'] > 0 ? number_format(($row['members'] + $row['visitors']) / $row['sessions']) : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.attendance.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            View All Sessions
        </a>
        <a href="{{ route('admin.reports.attendance') }}" class="inline-flex items-center px-4 py-2 border border-indigo-300 rounded-md text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100">
            Attendance Report
        </a>
    </div>
</div>
@endsection
