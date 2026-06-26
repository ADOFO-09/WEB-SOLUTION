@extends('layouts.admin')

@section('title', 'Income Statement')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.reports.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Income Statement</h1>
        </div>
        <div class="flex items-center space-x-2 no-print">
            <form action="{{ route('admin.reports.income-statement') }}" method="GET" class="flex items-center space-x-2">
                <select name="year" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                    @forelse($financialYears as $fy)
                        <option value="{{ $fy->start_date->year }}"
                            {{ $year == $fy->start_date->year ? 'selected' : '' }}>
                            {{ $fy->name }}{{ $fy->is_active ? ' ★' : '' }}
                        </option>
                    @empty
                        <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                    @endforelse
                </select>
                <select name="month" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                    <option value="">Full Year</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
            </form>
            <button type="button" onclick="window.print()"
                    class="px-3 py-2 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
        </div>
    </div>
@endsection

@push('styles')
<style>
@media print {
    /* Hide everything that doesn't belong on paper */
    .sidebar,
    .top-nav,
    .page-header,
    .no-print { display: none !important; }

    .main-content { margin-left: 0 !important; }
    .content-area { padding: 0 !important; }

    body, html { background: #fff !important; font-size: 12pt; }

    /* Print header — hidden on screen */
    .print-header { display: block !important; }

    /* Collapse spacing */
    .report-wrapper { max-width: 100% !important; padding: 0 !important; }

    /* Ensure table borders print */
    table { border-collapse: collapse; }

    /* Avoid page breaks inside sections */
    .report-section { break-inside: avoid; page-break-inside: avoid; margin-bottom: 1rem; }

    /* Bring out colors for print */
    .text-green-800 { color: #065f46 !important; }
    .text-green-600 { color: #16a34a !important; }
    .text-red-800   { color: #991b1b !important; }
    .text-red-600   { color: #dc2626 !important; }
    .bg-green-50    { background: #f0fdf4 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .bg-red-50      { background: #fff5f5 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}

/* Print header is hidden on screen */
.print-header { display: none; }
</style>
@endpush

@section('content')
@php
    $churchName = \App\Helpers\SettingHelper::churchName();
@endphp

<div class="report-wrapper max-w-4xl mx-auto space-y-6">

    {{-- Print-only header --}}
    <div class="print-header" style="text-align:center;padding-bottom:1.5rem;border-bottom:2px solid #1e3a5f;margin-bottom:1.5rem;">
        <h1 style="font-size:18pt;font-weight:700;color:#1e3a5f;margin:0 0 0.25rem;">{{ $churchName }}</h1>
        <h2 style="font-size:14pt;font-weight:600;color:#374151;margin:0 0 0.25rem;">Income Statement</h2>
        <p style="font-size:10pt;color:#6b7280;margin:0;">
            {{ $periodLabel }}
            &nbsp;&bull;&nbsp;
            Generated: {{ now()->format('d M Y, g:i A') }}
        </p>
    </div>

    {{-- Period heading (screen) --}}
    <div class="text-center py-4 no-print">
        <h2 class="text-xl font-bold text-gray-900">{{ $periodLabel }}</h2>
        @if($comparison['change'] != 0)
        <p class="text-sm mt-1 {{ $comparison['change'] > 0 ? 'text-green-600' : 'text-red-600' }}">
            {!! $comparison['change'] > 0 ? '&uarr;' : '&darr;' !!}
            {{ abs($comparison['change']) }}% vs previous period
        </p>
        @endif
    </div>

    {{-- Period + comparison for print --}}
    <div class="print-header" style="text-align:center;margin-bottom:1rem;">
        @if($comparison['change'] != 0)
        <p style="font-size:10pt;color:{{ $comparison['change'] > 0 ? '#16a34a' : '#dc2626' }};margin:0;">
            {!! $comparison['change'] > 0 ? '&uarr;' : '&darr;' !!}
            {{ abs($comparison['change']) }}% vs previous period
        </p>
        @endif
    </div>

    {{-- Income --}}
    <div class="report-section bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 bg-green-50 border-b">
            <h3 class="text-lg font-semibold text-green-800">Income</h3>
        </div>
        <div class="p-6">
            <table class="w-full">
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="py-3 text-gray-700">Tithes</td>
                        <td class="py-3 text-right font-medium">{{ $currencySymbol }} {{ number_format($income['tithes'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 text-gray-700">Offerings</td>
                        <td class="py-3 text-right font-medium">{{ $currencySymbol }} {{ number_format($income['offerings'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 text-gray-700">Donations</td>
                        <td class="py-3 text-right font-medium">{{ $currencySymbol }} {{ number_format($income['donations'], 2) }}</td>
                    </tr>
                    @if($offeringsByCategory->isNotEmpty())
                    <tr>
                        <td colspan="2" class="pt-4 pb-1">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Offerings by Category</p>
                        </td>
                    </tr>
                    @foreach($offeringsByCategory as $cat)
                    <tr>
                        <td class="py-2 pl-4 text-gray-500 text-sm">{{ $cat->incomeCategory->name ?? 'General' }}</td>
                        <td class="py-2 text-right text-sm text-gray-500">{{ $currencySymbol }} {{ number_format($cat->total, 2) }}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-green-200">
                        <td class="py-4 font-bold text-green-800">Total Income</td>
                        <td class="py-4 text-right text-xl font-bold text-green-600">{{ $currencySymbol }} {{ number_format($income['total'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Expenses --}}
    <div class="report-section bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 bg-red-50 border-b">
            <h3 class="text-lg font-semibold text-red-800">Expenses</h3>
        </div>
        <div class="p-6">
            <table class="w-full">
                <tbody class="divide-y divide-gray-100">
                    @forelse($expensesByCategory as $expense)
                    <tr>
                        <td class="py-3 text-gray-700">{{ $expense->expenseCategory->name ?? 'N/A' }}</td>
                        <td class="py-3 text-right font-medium">{{ $currencySymbol }} {{ number_format($expense->total, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="py-4 text-center text-gray-500">No expenses recorded for this period</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-red-200">
                        <td class="py-4 font-bold text-red-800">Total Expenses</td>
                        <td class="py-4 text-right text-xl font-bold text-red-600">{{ $currencySymbol }} {{ number_format($totalExpenses, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Net Income --}}
    <div class="report-section bg-white rounded-lg shadow-sm border p-6">
        <div class="flex justify-between items-center">
            <span class="text-xl font-bold {{ $netIncome >= 0 ? 'text-green-800' : 'text-red-800' }}">Net Income</span>
            <span class="text-3xl font-bold {{ $netIncome >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $currencySymbol }} {{ number_format(abs($netIncome), 2) }}
                @if($netIncome < 0)<span class="text-lg">(Deficit)</span>@endif
            </span>
        </div>
        @if($comparison['previous_income'] > 0)
        <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between text-sm text-gray-500">
            <span>Previous period income</span>
            <span>{{ $currencySymbol }} {{ number_format($comparison['previous_income'], 2) }}</span>
        </div>
        @endif
    </div>

    {{-- Print footer --}}
    <div class="print-header" style="text-align:center;margin-top:2rem;padding-top:1rem;border-top:1px solid #d1d5db;">
        <p style="font-size:9pt;color:#9ca3af;">
            {{ $churchName }} &bull; Confidential Financial Report &bull; {{ now()->format('d M Y') }}
        </p>
    </div>

</div>
@endsection
