@extends('admin.pdf.ministry-layout')

@section('pdf-title', 'Offerings — ' . $year)
@section('pdf-meta',
    ($filterMonth ? \Carbon\Carbon::create($year, $filterMonth, 1)->format('F') . ' ' : '') . $year
    . ($filterType ? ' &nbsp;&bull;&nbsp; Type: ' . ($types[$filterType] ?? $filterType) : '')
    . ' &nbsp;&bull;&nbsp; Records: ' . $offerings->count()
)

@section('content')

@php
$grandTotal = $offerings->sum('amount');
@endphp

<table class="stats-wrap">
    <tr>
        <td style="width:50%">
            <div class="stat-label">Total Offerings</div>
            <div class="stat-value">{{ $currencySymbol }} {{ number_format($grandTotal, 2) }}</div>
        </td>
        <td style="width:50%">
            <div class="stat-label">Number of Records</div>
            <div class="stat-value">{{ $offerings->count() }}</div>
        </td>
    </tr>
</table>

<table class="data">
    <thead>
        <tr>
            <th>Date</th>
            <th>Reference</th>
            <th>Type</th>
            <th>Description</th>
            <th>Payment Method</th>
            <th class="r">Amount ({{ $currencySymbol }})</th>
            <th>Recorded By</th>
        </tr>
    </thead>
    <tbody>
        @forelse($offerings as $offering)
        <tr>
            <td class="c">{{ $offering->offering_date instanceof \Carbon\Carbon ? $offering->offering_date->format('d M Y') : \Carbon\Carbon::parse($offering->offering_date)->format('d M Y') }}</td>
            <td>{{ $offering->reference_number }}</td>
            <td>{{ $types[$offering->offering_type] ?? ucfirst($offering->offering_type) }}</td>
            <td>{{ $offering->description ?: '—' }}</td>
            <td class="c">{{ ucfirst(str_replace('_', ' ', $offering->payment_method)) }}</td>
            <td class="r">{{ number_format($offering->amount, 2) }}</td>
            <td>{{ $offering->recordedBy?->name ?? '—' }}</td>
        </tr>
        @empty
        <tr class="empty"><td colspan="7">No offerings found for the selected period.</td></tr>
        @endforelse
        @if($offerings->count())
        <tr class="total">
            <td colspan="5" class="r">Grand Total</td>
            <td class="r">{{ number_format($grandTotal, 2) }}</td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>
@endsection
