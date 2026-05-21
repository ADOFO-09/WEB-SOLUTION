@extends('layouts.admin')

@section('title', 'Monthly Tithe Report')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.tithes.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Monthly Tithe Report</h1>
        </div>
        <form action="{{ route('admin.tithes.monthly-report') }}" method="GET" class="flex items-center space-x-2 mt-4 sm:mt-0">
            <select name="month" class="rounded-md border-gray-300 text-sm">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endforeach
            </select>
            <select name="year" class="rounded-md border-gray-300 text-sm">
                @foreach(range(date('Y'), date('Y') - 5) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Filter</button>
        </form>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Amount</div>
            <div class="text-2xl font-bold text-indigo-600">GH₵ {{ number_format($totalAmount, 2) }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Records</div>
            <div class="text-2xl font-bold text-gray-900">{{ $tithes->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Unique Members</div>
            <div class="text-2xl font-bold text-green-600">{{ $tithes->unique('member_id')->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Average Amount</div>
            <div class="text-2xl font-bold text-blue-600">
                GH₵ {{ $tithes->count() > 0 ? number_format($totalAmount / $tithes->count(), 2) : '0.00' }}
            </div>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    @if($byPaymentMethod->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">By Payment Method</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($byPaymentMethod as $method => $data)
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-sm font-medium text-gray-500 capitalize">{{ str_replace('_', ' ', $method) }}</div>
                <div class="text-xl font-bold text-gray-900">GH₵ {{ number_format($data['total'], 2) }}</div>
                <div class="text-xs text-gray-400">{{ $data['count'] }} record(s)</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Tithe Records Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">
                Records for {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}
            </h2>
            <span class="text-sm text-gray-500">{{ $tithes->count() }} record(s)</span>
        </div>
        @if($tithes->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($tithes as $tithe)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-mono text-gray-500">{{ $tithe->reference_number }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $tithe->member?->full_name ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $tithe->payment_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 capitalize">{{ str_replace('_', ' ', $tithe->payment_method) }}</td>
                        <td class="px-6 py-4 text-sm text-right font-semibold text-gray-900">GH₵ {{ number_format($tithe->amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-500">{{ $tithe->receipt_number ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-sm font-semibold text-gray-700">Total</td>
                        <td class="px-6 py-4 text-sm text-right font-bold text-indigo-600">GH₵ {{ number_format($totalAmount, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-sm">No tithe records for {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
