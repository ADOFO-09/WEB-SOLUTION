@extends('layouts.admin')

@section('title', 'Welfare Fund Summary')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Welfare Fund Summary</h1>
    </div>
@endsection

@section('content')
    <!-- Fund Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Collected</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $currencySymbol }} {{ number_format($fund['collected'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">All welfare contributions received</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Paid Out</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $currencySymbol }} {{ number_format($fund['paid_out'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Benefits + itemised expenses</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Fund Balance</p>
                    <p class="text-3xl font-bold {{ $fund['balance'] >= 0 ? 'text-green-700' : 'text-red-700' }} mt-1">
                        {{ $fund['balance'] < 0 ? '-' : '' }}{{ $currencySymbol }} {{ number_format(abs($fund['balance']), 2) }}
                    </p>
                </div>
                <div class="w-12 h-12 {{ $fund['balance'] >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 {{ $fund['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">
                {{ $fund['balance'] >= 0 ? 'Surplus — fund is healthy' : 'Deficit — contributions needed' }}
            </p>
        </div>
    </div>

    <!-- Recent Contributions -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Recent Contributions</h2>
            <a href="{{ route('admin.welfare.contributions.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">View all</a>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentContributions as $contribution)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $contribution->reference_number }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $contribution->member?->full_name ?? '—' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                        {{ date('M', mktime(0,0,0,$contribution->period_month,1)) }} {{ $contribution->period_year }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-green-600 text-right">
                        {{ $currencySymbol }} {{ number_format($contribution->amount, 2) }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $contribution->payment_date->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No contributions recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
