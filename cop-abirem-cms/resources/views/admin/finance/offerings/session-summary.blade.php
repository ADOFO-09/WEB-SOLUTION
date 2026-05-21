@extends('layouts.admin')

@section('title', 'Session Offering Summary')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.offerings.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Session Offering Summary</h1>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $session->serviceType?->name ?? 'Service' }} — {{ $session->service_date->format('D, d M Y') }}
                </p>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Collected</div>
            <div class="text-2xl font-bold text-green-600">{{ $currencySymbol }} {{ number_format($totalAmount, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Records</div>
            <div class="text-2xl font-bold text-gray-900">{{ $offerings->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Categories</div>
            <div class="text-2xl font-bold text-indigo-600">{{ $byCategory->count() }}</div>
        </div>
    </div>

    <!-- By Category Breakdown -->
    @if($byCategory->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Breakdown by Category</h2>
        <div class="space-y-3">
            @foreach($byCategory as $category)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <div class="text-sm font-medium text-gray-900">{{ $category['name'] }}</div>
                    <div class="text-xs text-gray-400">{{ $category['count'] }} record(s)</div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-bold text-gray-900">{{ $currencySymbol }} {{ number_format($category['total'], 2) }}</div>
                    @if($totalAmount > 0)
                    <div class="text-xs text-gray-400">{{ number_format(($category['total'] / $totalAmount) * 100, 1) }}%</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Offering Records -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Offering Records</h2>
            <span class="text-sm text-gray-500">{{ $offerings->count() }} record(s)</span>
        </div>
        @if($offerings->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contributor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($offerings as $offering)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-mono text-gray-500">{{ $offering->reference_number }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $offering->is_anonymous ? 'Anonymous' : ($offering->member?->full_name ?? 'Unknown') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $offering->incomeCategory?->name ?? 'General' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 capitalize">{{ str_replace('_', ' ', $offering->payment_method) }}</td>
                        <td class="px-6 py-4 text-sm text-right font-semibold text-gray-900">{{ $currencySymbol }} {{ number_format($offering->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-sm font-semibold text-gray-700">Total</td>
                        <td class="px-6 py-4 text-sm text-right font-bold text-green-600">{{ $currencySymbol }} {{ number_format($totalAmount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center text-gray-400">
            <p class="text-sm">No offerings recorded for this session.</p>
        </div>
        @endif
    </div>

    <!-- Session Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Session Details</h2>
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Service Type</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $session->serviceType?->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Date</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $session->service_date->format('d M Y') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Members</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ number_format($session->total_members) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Visitors</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ number_format($session->total_visitors) }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection
