@extends('layouts.member')

@section('title', 'My Tithes')

@section('header')
<h1 class="text-xl font-bold text-gray-900">Tithe History</h1>
@endsection

@section('content')
<!-- Summary & Filter -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 space-y-4 md:space-y-0">
    <div class="bg-white rounded-xl shadow-sm px-6 py-4">
        <span class="text-sm text-gray-500">Total Tithes ({{ $year }})</span>
        <span class="ml-3 text-2xl font-bold text-green-600">GH₵ {{ number_format($total, 2) }}</span>
    </div>
    <form action="{{ route('member.giving.tithes') }}" method="GET" class="flex items-center space-x-2">
        <label class="text-sm text-gray-600">Year:</label>
        <select name="year" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            @foreach($years as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>
</div>

<!-- Tithes List -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Method</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tithes as $tithe)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tithe->payment_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $tithe->reference_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($tithe->payment_method == 'cash') bg-green-100 text-green-800
                            @elseif($tithe->payment_method == 'mobile_money') bg-yellow-100 text-yellow-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $tithe->payment_method)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">GH₵ {{ number_format($tithe->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                        <p>No tithe records found for {{ $year }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($tithes->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $tithes->links() }}
    </div>
    @endif
</div>
@endsection
