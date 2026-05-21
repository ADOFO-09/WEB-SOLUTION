@extends('layouts.admin')

@section('title', 'Finance Dashboard')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Finance Dashboard</h1>
        <form action="{{ route('admin.dashboard.finance') }}" method="GET" class="flex items-center space-x-2 mt-4 sm:mt-0">
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
    <!-- Year Totals Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Tithes</div>
            <div class="text-xl font-bold text-indigo-600">{{ $currencySymbol }} {{ number_format($yearTotals['tithes'], 2) }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $year }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Offerings</div>
            <div class="text-xl font-bold text-green-600">{{ $currencySymbol }} {{ number_format($yearTotals['offerings'], 2) }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $year }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Donations</div>
            <div class="text-xl font-bold text-blue-600">{{ $currencySymbol }} {{ number_format($yearTotals['donations'], 2) }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $year }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Expenses</div>
            <div class="text-xl font-bold text-red-600">{{ $currencySymbol }} {{ number_format($yearTotals['expenses'], 2) }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $year }}</div>
        </div>
    </div>

    <!-- Net Summary Banner -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
            <div>
                <div class="text-sm font-medium text-gray-500">Total Income</div>
                <div class="text-3xl font-bold text-gray-900">{{ $currencySymbol }} {{ number_format($yearTotals['total_income'], 2) }}</div>
            </div>
            <div class="flex items-center justify-center">
                <span class="text-2xl text-gray-300">→</span>
            </div>
            <div>
                <div class="text-sm font-medium text-gray-500">Net Position</div>
                <div class="text-3xl font-bold {{ $yearTotals['net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $currencySymbol }} {{ number_format(abs($yearTotals['net']), 2) }}
                    <span class="text-base font-normal">{{ $yearTotals['net'] >= 0 ? 'surplus' : 'deficit' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Monthly Breakdown — {{ $year }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tithes</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Offerings</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Donations</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Income</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Expenses</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Net</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($monthlyData as $row)
                    @php
                        $income = $row['tithes'] + $row['offerings'] + $row['donations'];
                        $net    = $income - $row['expenses'];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $row['month'] }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-700">{{ number_format($row['tithes'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-700">{{ number_format($row['offerings'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-700">{{ number_format($row['donations'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">{{ number_format($income, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-red-600">{{ number_format($row['expenses'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right font-semibold {{ $net >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($net, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Tithe Contributors -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Top Tithe Contributors — {{ $year }}</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($topTithers as $index => $tithe)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-bold text-gray-400 w-6">{{ $index + 1 }}</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $tithe->member?->full_name ?? 'Unknown Member' }}
                        </span>
                    </div>
                    <span class="text-sm font-semibold text-indigo-600">{{ $currencySymbol }} {{ number_format($tithe->total, 2) }}</span>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-400 text-sm">No tithe records for {{ $year }}</div>
                @endforelse
            </div>
        </div>

        <!-- Expenses by Category -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Expenses by Category — {{ $year }}</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($expensesByCategory as $expense)
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-900">
                        {{ $expense->expenseCategory?->name ?? 'Uncategorized' }}
                    </span>
                    <div class="text-right">
                        <span class="text-sm font-semibold text-red-600">{{ $currencySymbol }} {{ number_format($expense->total, 2) }}</span>
                        @if($yearTotals['expenses'] > 0)
                        <div class="text-xs text-gray-400">{{ number_format(($expense->total / $yearTotals['expenses']) * 100, 1) }}%</div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-400 text-sm">No expense records for {{ $year }}</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.tithes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            View Tithes
        </a>
        <a href="{{ route('admin.offerings.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            View Offerings
        </a>
        <a href="{{ route('admin.donations.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            View Donations
        </a>
        <a href="{{ route('admin.expenses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            View Expenses
        </a>
        <a href="{{ route('admin.reports.income-statement') }}" class="inline-flex items-center px-4 py-2 border border-indigo-300 rounded-md text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100">
            Income Statement
        </a>
    </div>
</div>
@endsection
