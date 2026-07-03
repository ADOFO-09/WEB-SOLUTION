@extends('layouts.admin')

@section('title', 'Funeral Spending Report')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Funeral Spending Report</h1>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <form action="{{ route('admin.funeral.reports.spending') }}" method="GET"
                  class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Year</label>
                    <input type="number" name="year" value="{{ $year }}"
                           placeholder="{{ date('Y') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-3 py-2 rounded-md hover:bg-indigo-700 text-sm">Filter</button>
                    <a href="{{ route('admin.funeral.reports.spending') }}" class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 text-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Totals -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Amount Donated</div>
            <div class="text-2xl font-bold text-gray-900">{{ $currencySymbol }} {{ number_format($summary['total_main'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Other Expenses</div>
            <div class="text-2xl font-bold text-orange-600">{{ $currencySymbol }} {{ number_format($summary['total_expenses'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Grand Total</div>
            <div class="text-2xl font-bold text-red-600">{{ $currencySymbol }} {{ number_format($summary['grand_total'], 2) }}</div>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Monthly Breakdown</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount Donated</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Other Expenses</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase"># Benefits</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($summary['by_month'] as $row)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $row['label'] }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-600">{{ $currencySymbol }} {{ number_format($row['main'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right text-orange-600">{{ $currencySymbol }} {{ number_format($row['other'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-gray-900">{{ $currencySymbol }} {{ number_format($row['total'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-500">{{ $row['count'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No data for the selected period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
