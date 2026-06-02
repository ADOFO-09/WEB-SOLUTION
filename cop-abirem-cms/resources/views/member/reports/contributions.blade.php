@extends('layouts.member')

@section('title', 'Contributions Report')

@section('header')
<h2 class="text-lg font-semibold text-gray-800">Contributions Report</h2>
@endsection

@push('styles')
<style>
/* ── Print styles ───────────────────────────────────── */
@media print {
    .sidebar, .top-nav, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .content-area { padding: 0 !important; }
    body, html { background: #fff !important; font-size: 11pt; }
    .print-header { display: block !important; }
    .report-section { break-inside: avoid; page-break-inside: avoid; margin-bottom: 1rem; border: 1px solid #e5e7eb; border-radius: 0; }
    .report-section-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    table { border-collapse: collapse; width: 100%; }
    td, th { padding: 0.35rem 0.5rem !important; font-size: 10pt; }
}
.print-header { display: none; }

/* ── Summary cards ──────────────────────────────────── */
.contrib-card {
    background: #fff;
    border-radius: 0.75rem;
    padding: 1.25rem 1.5rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
}
.contrib-card-value { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; line-height: 1.1; }
.contrib-card-label { font-size: 0.75rem; color: #6b7280; font-weight: 500; margin-top: 0.2rem; text-transform: uppercase; letter-spacing: 0.05em; }

/* ── Section toggle ─────────────────────────────────── */
.section-toggle {
    display: flex; align-items: center; justify-content: space-between;
    width: 100%; padding: 0.875rem 1.25rem; border: none; cursor: pointer;
    background: transparent; font-family: inherit; font-size: 0.9rem; font-weight: 600;
}
.section-toggle svg { width: 16px; height: 16px; transition: transform .2s; }
.section-toggle.open svg { transform: rotate(180deg); }

/* ── Monthly table ──────────────────────────────────── */
.monthly-bar {
    display: inline-block; height: 8px; border-radius: 4px; vertical-align: middle;
}
</style>
@endpush

@section('content')
@php
    $sym = $currencySymbol;
    $churchName = \App\Models\Setting::get('church_name', 'Church of Pentecost - Abirem');
    $fmt = fn($n) => $sym . ' ' . number_format($n, 2);
@endphp

{{-- ── Print-only header ───────────────────────────────── --}}
<div class="print-header" style="text-align:center;padding-bottom:1.25rem;border-bottom:2px solid #1e3a5f;margin-bottom:1.5rem;">
    <h1 style="font-size:16pt;font-weight:700;color:#1e3a5f;margin:0 0 .2rem;">{{ $churchName }}</h1>
    <h2 style="font-size:13pt;font-weight:600;color:#374151;margin:0 0 .2rem;">Member Contributions Report</h2>
    <p style="font-size:9pt;color:#6b7280;margin:0;">
        {{ $member->full_name }} ({{ $member->member_id }})
        &bull; {{ $periodLabel }}
        &bull; Generated: {{ now()->format($dateFormat . ', ' . $timeFormat) }}
    </p>
</div>

<div class="max-w-4xl mx-auto space-y-6">

    {{-- ── Filter bar ─────────────────────────────────────── --}}
    <div class="no-print flex flex-wrap items-center gap-3 bg-white rounded-xl border border-gray-200 px-4 py-3 shadow-sm">
        <form action="{{ route('member.reports.contributions') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-600">Year</label>
                <select name="year" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach($years as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-600">Month</label>
                <select name="month" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All months</option>
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0,0,0,$m,1)) }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="ml-auto">
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>
            </div>
        </form>
    </div>

    {{-- ── Period heading ──────────────────────────────────── --}}
    <div class="text-center no-print">
        <h3 class="text-xl font-bold text-gray-900">{{ $periodLabel }}</h3>
        @if($change !== null)
        <p class="text-sm mt-1 {{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {!! $change >= 0 ? '&uarr;' : '&darr;' !!} {{ abs($change) }}% vs previous period
            <span class="text-gray-400 ml-1">({{ $sym }} {{ number_format($prevTotal, 2) }})</span>
        </p>
        @endif
    </div>

    {{-- ── Summary cards ───────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="contrib-card md:col-span-1 border-l-4 border-indigo-500">
            <div class="contrib-card-value text-indigo-700">{{ $fmt($totals['grand']) }}</div>
            <div class="contrib-card-label">Total</div>
        </div>
        <div class="contrib-card border-l-4 border-blue-400">
            <div class="contrib-card-value text-blue-700">{{ $fmt($totals['tithes']) }}</div>
            <div class="contrib-card-label">Tithes</div>
        </div>
        <div class="contrib-card border-l-4 border-green-400">
            <div class="contrib-card-value text-green-700">{{ $fmt($totals['offerings']) }}</div>
            <div class="contrib-card-label">Offerings</div>
        </div>
        <div class="contrib-card border-l-4 border-pink-400">
            <div class="contrib-card-value text-pink-700">{{ $fmt($totals['donations']) }}</div>
            <div class="contrib-card-label">Donations</div>
        </div>
        <div class="contrib-card border-l-4 border-amber-400">
            <div class="contrib-card-value text-amber-700">{{ $fmt($totals['pledges']) }}</div>
            <div class="contrib-card-label">Pledge Payments</div>
        </div>
    </div>

    {{-- ── Monthly breakdown table (full-year view) ─────────── --}}
    @if(!$month && count($monthlyBreakdown))
    <div class="report-section bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="report-section-header px-5 py-3 bg-gray-50 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Monthly Breakdown</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-blue-600 uppercase tracking-wider">Tithes</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-green-600 uppercase tracking-wider">Offerings</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-pink-600 uppercase tracking-wider">Donations</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-amber-600 uppercase tracking-wider">Pledges</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-indigo-600 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php $hasAny = false; @endphp
                    @foreach($monthlyBreakdown as $row)
                    @php
                        $rowTotal = $row['tithes'] + $row['offerings'] + $row['donations'] + $row['pledges'];
                        if($rowTotal > 0) $hasAny = true;
                    @endphp
                    <tr class="{{ $rowTotal > 0 ? 'hover:bg-gray-50' : 'text-gray-300' }}">
                        <td class="px-4 py-2.5 font-medium {{ $rowTotal > 0 ? 'text-gray-800' : 'text-gray-300' }}">
                            {{ $row['month'] }}
                        </td>
                        <td class="px-4 py-2.5 text-right {{ $row['tithes'] > 0 ? 'text-blue-700' : 'text-gray-300' }}">
                            {{ $row['tithes'] > 0 ? number_format($row['tithes'], 2) : '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-right {{ $row['offerings'] > 0 ? 'text-green-700' : 'text-gray-300' }}">
                            {{ $row['offerings'] > 0 ? number_format($row['offerings'], 2) : '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-right {{ $row['donations'] > 0 ? 'text-pink-700' : 'text-gray-300' }}">
                            {{ $row['donations'] > 0 ? number_format($row['donations'], 2) : '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-right {{ $row['pledges'] > 0 ? 'text-amber-700' : 'text-gray-300' }}">
                            {{ $row['pledges'] > 0 ? number_format($row['pledges'], 2) : '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-right font-semibold {{ $rowTotal > 0 ? 'text-indigo-700' : 'text-gray-300' }}">
                            {{ $rowTotal > 0 ? $sym . ' ' . number_format($rowTotal, 2) : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200 bg-gray-50 font-bold">
                        <td class="px-4 py-3 text-gray-800">Total</td>
                        <td class="px-4 py-3 text-right text-blue-700">{{ $sym }} {{ number_format($totals['tithes'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-green-700">{{ $sym }} {{ number_format($totals['offerings'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-pink-700">{{ $sym }} {{ number_format($totals['donations'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-amber-700">{{ $sym }} {{ number_format($totals['pledges'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-indigo-700">{{ $sym }} {{ number_format($totals['grand'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Tithes ───────────────────────────────────────────── --}}
    <div class="report-section bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" x-data="{ open: true }">
        <button type="button" class="section-toggle no-print" :class="{ open }" @click="open = !open"
                style="background:#eff6ff;border-bottom:1px solid #dbeafe;">
            <span class="text-blue-800 font-semibold">Tithes
                <span class="ml-2 text-sm font-normal text-blue-500">({{ $tithes->count() }} record{{ $tithes->count() != 1 ? 's' : '' }})</span>
            </span>
            <svg fill="none" stroke="#3b82f6" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
            </svg>
        </button>
        {{-- Print heading --}}
        <div class="report-section-header print-header" style="background:#eff6ff;padding:.5rem 1rem;border-bottom:1px solid #dbeafe;">
            <strong style="color:#1d4ed8;">Tithes</strong>
        </div>
        <div x-show="open" x-transition style="overflow:hidden;">
            @if($tithes->isNotEmpty())
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($tithes as $tithe)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 text-gray-700">{{ $tithe->payment_date->format($dateFormat) }}</td>
                        <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $tithe->reference_number }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ ucfirst(str_replace('_', ' ', $tithe->payment_method)) }}</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-blue-700">{{ $sym }} {{ number_format($tithe->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-blue-100 bg-blue-50">
                        <td colspan="3" class="px-4 py-3 font-bold text-blue-800">Total Tithes</td>
                        <td class="px-4 py-3 text-right font-bold text-blue-700">{{ $sym }} {{ number_format($totals['tithes'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            @else
            <p class="px-4 py-6 text-center text-sm text-gray-400">No tithe records for this period.</p>
            @endif
        </div>
    </div>

    {{-- ── Offerings ─────────────────────────────────────────── --}}
    <div class="report-section bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" x-data="{ open: true }">
        <button type="button" class="section-toggle no-print" :class="{ open }" @click="open = !open"
                style="background:#f0fdf4;border-bottom:1px solid #bbf7d0;">
            <span class="text-green-800 font-semibold">Offerings
                <span class="ml-2 text-sm font-normal text-green-500">({{ $offerings->count() }} record{{ $offerings->count() != 1 ? 's' : '' }})</span>
            </span>
            <svg fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
            </svg>
        </button>
        <div class="report-section-header print-header" style="background:#f0fdf4;padding:.5rem 1rem;border-bottom:1px solid #bbf7d0;">
            <strong style="color:#15803d;">Offerings</strong>
        </div>
        <div x-show="open" x-transition style="overflow:hidden;">
            @if($offerings->isNotEmpty())
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($offerings as $offering)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 text-gray-700">{{ $offering->payment_date->format($dateFormat) }}</td>
                        <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $offering->reference_number }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $offering->offeringType->name ?? 'General' }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ ucfirst(str_replace('_', ' ', $offering->payment_method)) }}</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-green-700">{{ $sym }} {{ number_format($offering->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-green-100 bg-green-50">
                        <td colspan="4" class="px-4 py-3 font-bold text-green-800">Total Offerings</td>
                        <td class="px-4 py-3 text-right font-bold text-green-700">{{ $sym }} {{ number_format($totals['offerings'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            @else
            <p class="px-4 py-6 text-center text-sm text-gray-400">No offering records for this period.</p>
            @endif
        </div>
    </div>

    {{-- ── Donations ─────────────────────────────────────────── --}}
    <div class="report-section bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" x-data="{ open: true }">
        <button type="button" class="section-toggle no-print" :class="{ open }" @click="open = !open"
                style="background:#fdf2f8;border-bottom:1px solid #fbcfe8;">
            <span class="text-pink-800 font-semibold">Donations
                <span class="ml-2 text-sm font-normal text-pink-400">({{ $donations->count() }} record{{ $donations->count() != 1 ? 's' : '' }})</span>
            </span>
            <svg fill="none" stroke="#ec4899" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
            </svg>
        </button>
        <div class="report-section-header print-header" style="background:#fdf2f8;padding:.5rem 1rem;border-bottom:1px solid #fbcfe8;">
            <strong style="color:#be185d;">Donations</strong>
        </div>
        <div x-show="open" x-transition style="overflow:hidden;">
            @if($donations->isNotEmpty())
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Receipt</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($donations as $donation)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 text-gray-700">{{ $donation->payment_date->format($dateFormat) }}</td>
                        <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $donation->receipt_number ?? $donation->reference_number }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $donation->project?->name ?? 'General' }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ ucfirst(str_replace('_', ' ', $donation->payment_method)) }}</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-pink-700">{{ $sym }} {{ number_format($donation->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-pink-100 bg-pink-50">
                        <td colspan="4" class="px-4 py-3 font-bold text-pink-800">Total Donations</td>
                        <td class="px-4 py-3 text-right font-bold text-pink-700">{{ $sym }} {{ number_format($totals['donations'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            @else
            <p class="px-4 py-6 text-center text-sm text-gray-400">No donation records for this period.</p>
            @endif
        </div>
    </div>

    {{-- ── Pledge Payments ──────────────────────────────────── --}}
    <div class="report-section bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" x-data="{ open: true }">
        <button type="button" class="section-toggle no-print" :class="{ open }" @click="open = !open"
                style="background:#fffbeb;border-bottom:1px solid #fde68a;">
            <span class="text-amber-800 font-semibold">Pledge Payments
                <span class="ml-2 text-sm font-normal text-amber-500">({{ $pledgePayments->count() }} record{{ $pledgePayments->count() != 1 ? 's' : '' }})</span>
            </span>
            <svg fill="none" stroke="#f59e0b" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
            </svg>
        </button>
        <div class="report-section-header print-header" style="background:#fffbeb;padding:.5rem 1rem;border-bottom:1px solid #fde68a;">
            <strong style="color:#92400e;">Pledge Payments</strong>
        </div>
        <div x-show="open" x-transition style="overflow:hidden;">
            @if($pledgePayments->isNotEmpty())
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Receipt</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pledge</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($pledgePayments as $pp)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 text-gray-700">{{ $pp->payment_date->format($dateFormat) }}</td>
                        <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $pp->receipt_number ?? $pp->reference_number }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $pp->pledge->title ?? 'Pledge' }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ ucfirst(str_replace('_', ' ', $pp->payment_method)) }}</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-amber-700">{{ $sym }} {{ number_format($pp->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-amber-100 bg-amber-50">
                        <td colspan="4" class="px-4 py-3 font-bold text-amber-800">Total Pledge Payments</td>
                        <td class="px-4 py-3 text-right font-bold text-amber-700">{{ $sym }} {{ number_format($totals['pledges'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            @else
            <p class="px-4 py-6 text-center text-sm text-gray-400">No pledge payments for this period.</p>
            @endif
        </div>
    </div>

    {{-- ── Grand total ──────────────────────────────────────── --}}
    <div class="report-section bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex justify-between items-center">
            <span class="text-lg font-bold text-indigo-900">Grand Total Contributions</span>
            <span class="text-2xl font-extrabold text-indigo-700">{{ $sym }} {{ number_format($totals['grand'], 2) }}</span>
        </div>
        @if($change !== null)
        <p class="mt-2 text-sm {{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {!! $change >= 0 ? '&uarr;' : '&darr;' !!} {{ abs($change) }}% compared to previous period
            ({{ $sym }} {{ number_format($prevTotal, 2) }})
        </p>
        @endif
    </div>

    {{-- ── Print footer ─────────────────────────────────────── --}}
    <div class="print-header" style="text-align:center;margin-top:2rem;padding-top:1rem;border-top:1px solid #d1d5db;">
        <p style="font-size:8pt;color:#9ca3af;">
            {{ $churchName }} &bull; Confidential Member Report &bull; {{ now()->format($dateFormat) }}
        </p>
    </div>

</div>
@endsection
