@extends('layouts.admin')

@section('title', 'Tithe Report')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.reports.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Tithe Report</h1>
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
            <select name="month" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                <option value="">Full Year</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endfor
            </select>
        </form>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-green-600">GHâ‚µ {{ number_format($totals['amount'], 2) }}</p>
            <p class="text-sm text-gray-500">Total Tithes</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-blue-600">{{ $totals['members'] }}</p>
            <p class="text-sm text-gray-500">Tithe Payers</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-gray-900">{{ $totals['payments'] }}</p>
            <p class="text-sm text-gray-500">Total Payments</p>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold">Monthly Breakdown - {{ $year }}</h3></div>
        <div class="p-6 overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Members</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($monthlyBreakdown as $data)
                    <tr>
                        <td class="px-4 py-3">{{ $data['month'] }}</td>
                        <td class="px-4 py-3 text-right font-medium text-green-600">GHâ‚µ {{ number_format($data['amount'], 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ $data['count'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Contributors -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold">Top Contributors</h3></div>
        <div class="p-6 overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Payments</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($memberTithes->take(20) as $index => $item)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">{{ $item->member->full_name ?? 'Unknown' }}</td>
                        <td class="px-4 py-3 text-right">{{ $item->payments }}</td>
                        <td class="px-4 py-3 text-right font-bold text-green-600">GHâ‚µ {{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
