@extends('layouts.member')

@section('title', 'Funeral Fund')

@section('header')
<h1 class="text-xl font-bold text-gray-900">Funeral Fund</h1>
@endsection

@section('content')

{{-- Enrollment & Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0
             {{ $member->funeral_enrolled ? 'bg-green-100' : 'bg-gray-100' }}">
            @if($member->funeral_enrolled)
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            @else
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            @endif
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Enrollment Status</p>
            @if($member->funeral_enrolled)
                <p class="text-base font-bold text-green-700">Enrolled</p>
                @if($member->funeral_start_date)
                <p class="text-xs text-gray-400">Since {{ \Carbon\Carbon::parse($member->funeral_start_date)->format('M Y') }}</p>
                @endif
            @else
                <p class="text-base font-bold text-gray-500">Not Enrolled</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Dues Paid ({{ $year }})</p>
            <p class="text-xl font-bold text-gray-900">{{ $currencySymbol }} {{ number_format($totalPaid, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Support Received</p>
            <p class="text-xl font-bold text-gray-900">{{ $currencySymbol }} {{ number_format($totalBenefits, 2) }}</p>
            <p class="text-xs text-gray-400">{{ $benefits->count() }} {{ Str::plural('disbursement', $benefits->count()) }}</p>
        </div>
    </div>

</div>

{{-- Dues Payments Section --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h3 class="font-semibold text-gray-900">Dues Payments</h3>
            <p class="text-xs text-gray-500 mt-0.5">Your funeral fund contributions for {{ $year }}</p>
        </div>
        <form action="{{ route('member.funeral.index') }}" method="GET" class="flex items-center gap-2">
            <label class="text-sm text-gray-600">Year:</label>
            <select name="year" onchange="this.form.submit()"
                    class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach($years as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($contributions as $contribution)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ \Carbon\Carbon::create($contribution->period_year, $contribution->period_month, 1)->format('F Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                        {{ $contribution->reference_number }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $contribution->payment_date->format($dateFormat) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($contribution->payment_method == 'cash') bg-green-100 text-green-800
                            @elseif($contribution->payment_method == 'mobile_money') bg-yellow-100 text-yellow-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $contribution->payment_method)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">
                        {{ $currencySymbol }} {{ number_format($contribution->amount, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                        <p class="font-medium">No funeral dues recorded for {{ $year }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($contributions->count() > 0)
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="4" class="px-6 py-3 text-sm font-semibold text-gray-700 text-right">Total for {{ $year }}:</td>
                    <td class="px-6 py-3 text-sm font-bold text-right text-blue-700">{{ $currencySymbol }} {{ number_format($totalPaid, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if($contributions->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $contributions->links() }}
    </div>
    @endif
</div>

{{-- Benefits / Support Received Section --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900">Support Received</h3>
        <p class="text-xs text-gray-500 mt-0.5">All funeral support disbursements made to you</p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Funeral Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deceased</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($benefits as $benefit)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $benefit->funeral_date->format($dateFormat) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $benefit->deceased_name ?: '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $benefit->venue ?: '—' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                        {{ $benefit->description ?: '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-amber-700">
                        {{ $currencySymbol }} {{ number_format($benefit->amount_donated, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="font-medium">No funeral support received yet</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($benefits->count() > 0)
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="4" class="px-6 py-3 text-sm font-semibold text-gray-700 text-right">Total Support:</td>
                    <td class="px-6 py-3 text-sm font-bold text-right text-amber-700">{{ $currencySymbol }} {{ number_format($totalBenefits, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection
