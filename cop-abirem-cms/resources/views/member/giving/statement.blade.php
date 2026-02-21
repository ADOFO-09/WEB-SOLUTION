@extends('layouts.member')

@section('title', 'Giving Statement')

@section('header')
<h1 class="text-xl font-bold text-gray-900">Giving Statement</h1>
@endsection

@section('content')
<!-- Header with Download -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 space-y-4 md:space-y-0">
    <form action="{{ route('member.giving.statement') }}" method="GET" class="flex items-center space-x-2">
        <label class="text-sm text-gray-600">Year:</label>
        <select name="year" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            @foreach($years as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>
    <div class="flex space-x-3 no-print">
        <a href="{{ route('member.giving.statement.download', ['year' => $year]) }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download CSV
        </a>
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print
        </button>
    </div>
</div>

<!-- Statement -->
<div class="bg-white rounded-xl shadow-sm p-8" id="statement">
    <!-- Header -->
    <div class="text-center border-b pb-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Church of Pentecost - Abirem</h1>
        <p class="text-gray-600">Giving Statement for {{ $year }}</p>
    </div>
    
    <!-- Member Info -->
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Member Name</p>
                <p class="font-medium">{{ $member->title }} {{ $member->full_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Member ID</p>
                <p class="font-medium">{{ $member->member_id }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Statement Period</p>
                <p class="font-medium">January 1 - December 31, {{ $year }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Generated</p>
                <p class="font-medium">{{ now()->format('F d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Tithes Section -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-3 border-b pb-2">Tithes</h3>
        @if($tithes->count() > 0)
        <table class="min-w-full">
            <thead>
                <tr class="text-left text-sm text-gray-500">
                    <th class="pb-2">Date</th>
                    <th class="pb-2">Reference</th>
                    <th class="pb-2 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($tithes as $tithe)
                <tr class="border-b border-gray-100">
                    <td class="py-2">{{ $tithe->payment_date->format('M d, Y') }}</td>
                    <td class="py-2">{{ $tithe->reference_number }}</td>
                    <td class="py-2 text-right">GH₵ {{ number_format($tithe->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-bold">
                    <td colspan="2" class="pt-2">Total Tithes</td>
                    <td class="pt-2 text-right">GH₵ {{ number_format($totals['tithes'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
        @else
        <p class="text-gray-500 text-sm">No tithe records for this period.</p>
        @endif
    </div>

    <!-- Offerings Section -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-3 border-b pb-2">Offerings</h3>
        @if($offerings->count() > 0)
        <table class="min-w-full">
            <thead>
                <tr class="text-left text-sm text-gray-500">
                    <th class="pb-2">Date</th>
                    <th class="pb-2">Type</th>
                    <th class="pb-2 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($offerings as $offering)
                <tr class="border-b border-gray-100">
                    <td class="py-2">{{ $offering->payment_date->format('M d, Y') }}</td>
                    <td class="py-2">{{ $offering->offeringType?->name ?? 'General' }}</td>
                    <td class="py-2 text-right">GH₵ {{ number_format($offering->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-bold">
                    <td colspan="2" class="pt-2">Total Offerings</td>
                    <td class="pt-2 text-right">GH₵ {{ number_format($totals['offerings'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
        @else
        <p class="text-gray-500 text-sm">No offering records for this period.</p>
        @endif
    </div>

    <!-- Donations Section -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-3 border-b pb-2">Donations</h3>
        @if($donations->count() > 0)
        <table class="min-w-full">
            <thead>
                <tr class="text-left text-sm text-gray-500">
                    <th class="pb-2">Date</th>
                    <th class="pb-2">Project</th>
                    <th class="pb-2 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($donations as $donation)
                <tr class="border-b border-gray-100">
                    <td class="py-2">{{ $donation->payment_date->format('M d, Y') }}</td>
                    <td class="py-2">{{ $donation->project?->name ?? 'General Fund' }}</td>
                    <td class="py-2 text-right">GH₵ {{ number_format($donation->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-bold">
                    <td colspan="2" class="pt-2">Total Donations</td>
                    <td class="pt-2 text-right">GH₵ {{ number_format($totals['donations'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
        @else
        <p class="text-gray-500 text-sm">No donation records for this period.</p>
        @endif
    </div>

    <!-- Grand Total -->
    <div class="border-t-2 border-gray-300 pt-4">
        <div class="flex justify-between items-center text-xl font-bold">
            <span>Grand Total</span>
            <span class="text-blue-600">GH₵ {{ number_format($totals['grand_total'], 2) }}</span>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-8 pt-6 border-t text-center text-sm text-gray-500">
        <p>Thank you for your faithful giving to the Lord's work.</p>
        <p class="mt-1">This statement is for your records. For any questions, please contact the church office.</p>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        #statement, #statement * { visibility: visible; }
        #statement { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
    }
</style>
@endpush
@endsection
