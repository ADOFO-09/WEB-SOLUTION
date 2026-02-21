@extends('layouts.member')

@section('title', 'My Offerings')

@section('header')
<h1 class="text-xl font-bold text-gray-900">Offering History</h1>
@endsection

@section('content')
<!-- Summary & Filter -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 space-y-4 md:space-y-0">
    <div class="bg-white rounded-xl shadow-sm px-6 py-4">
        <span class="text-sm text-gray-500">Total Offerings ({{ $year }})</span>
        <span class="ml-3 text-2xl font-bold text-blue-600">GH₵ {{ number_format($total, 2) }}</span>
    </div>
    <form action="{{ route('member.giving.offerings') }}" method="GET" class="flex items-center space-x-2">
        <label class="text-sm text-gray-600">Year:</label>
        <select name="year" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            @foreach($years as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>
</div>

<!-- Offerings List -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Method</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($offerings as $offering)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $offering->payment_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $offering->offeringType?->name ?? 'General' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $offering->reference_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($offering->payment_method == 'cash') bg-green-100 text-green-800
                            @elseif($offering->payment_method == 'mobile_money') bg-yellow-100 text-yellow-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $offering->payment_method)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">GH₵ {{ number_format($offering->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                        <p>No offering records found for {{ $year }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($offerings->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $offerings->links() }}
    </div>
    @endif
</div>
@endsection
