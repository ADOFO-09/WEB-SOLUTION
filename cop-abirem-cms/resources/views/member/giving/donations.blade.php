@extends('layouts.member')

@section('title', 'My Donations')

@section('header')
<h1 class="text-xl font-bold text-gray-900">Donation History</h1>
@endsection

@section('content')
<!-- Summary & Filter -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 space-y-4 md:space-y-0">
    <div class="bg-white rounded-xl shadow-sm px-6 py-4">
        <span class="text-sm text-gray-500">Total Donations ({{ $year }})</span>
        <span class="ml-3 text-2xl font-bold text-purple-600">{{ $currencySymbol }} {{ number_format($total, 2) }}</span>
    </div>
    <form action="{{ route('member.giving.donations') }}" method="GET" class="flex items-center space-x-2">
        <label class="text-sm text-gray-600">Year:</label>
        <select name="year" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            @foreach($years as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>
</div>

<!-- Donations List -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Receipt #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($donations as $donation)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $donation->payment_date->format($dateFormat) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $donation->receipt_number ?? $donation->reference_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($donation->donation_type == 'cash')
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Cash</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">In-Kind</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $donation->project?->name ?? 'General' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                        @if($donation->donation_type == 'cash')
                        {{ $currencySymbol }} {{ number_format($donation->amount, 2) }}
                        @else
                        <span class="text-gray-500">{{ Str::limit($donation->in_kind_description, 20) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <p>No donation records found for {{ $year }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($donations->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $donations->links() }}
    </div>
    @endif
</div>
@endsection
