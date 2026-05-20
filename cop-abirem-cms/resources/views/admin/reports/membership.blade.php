@extends('layouts.admin')

@section('title', 'Membership Report')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.reports.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Membership Statistics</h1>
        </div>
        <form action="" method="GET" class="flex items-center space-x-2">
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
@endsection

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-gray-900">{{ number_format($totals['total']) }}</p>
            <p class="text-sm text-gray-500">Total Members</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-green-600">{{ number_format($totals['active']) }}</p>
            <p class="text-sm text-gray-500">Active Members</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-blue-600">{{ number_format($totals['new_this_year']) }}</p>
            <p class="text-sm text-gray-500">New in {{ $year }}</p>
        </div>
    </div>

    <!-- Demographics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Gender -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">By Gender</h3>
            @foreach($demographics['by_gender'] as $gender => $count)
            <div class="flex justify-between py-2 border-b last:border-0">
                <span class="capitalize">{{ $gender ?? 'Unknown' }}</span>
                <span class="font-medium">{{ $count }}</span>
            </div>
            @endforeach
        </div>

        <!-- Marital Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">By Marital Status</h3>
            @foreach($demographics['by_marital_status'] as $status => $count)
            <div class="flex justify-between py-2 border-b last:border-0">
                <span class="capitalize">{{ str_replace('_', ' ', $status ?? 'Unknown') }}</span>
                <span class="font-medium">{{ $count }}</span>
            </div>
            @endforeach
        </div>

        <!-- Employment -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">By Employment</h3>
            @foreach($demographics['by_employment'] as $status => $count)
            <div class="flex justify-between py-2 border-b last:border-0">
                <span class="capitalize">{{ str_replace('_', ' ', $status ?? 'Unknown') }}</span>
                <span class="font-medium">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Growth Trend -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Monthly Growth - {{ $year }}</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">New Members</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total (End)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($growthTrend as $data)
                    <tr>
                        <td class="px-4 py-3 text-sm">{{ $data['month'] }}</td>
                        <td class="px-4 py-3 text-sm text-right {{ $data['new_members'] > 0 ? 'text-green-600 font-medium' : '' }}">
                            {{ $data['new_members'] > 0 ? '+' : '' }}{{ $data['new_members'] }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-medium">{{ number_format($data['total_at_end']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Status Breakdown</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($statusBreakdown as $status => $count)
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-2xl font-bold {{ $status == 'active' ? 'text-green-600' : 'text-gray-600' }}">{{ $count }}</p>
                <p class="text-sm text-gray-500 capitalize">{{ $status }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
