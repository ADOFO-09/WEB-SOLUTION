@extends('admin.pdf.ministry-layout')

@section('pdf-title', 'Welfare Benefits — ' . $year)
@section('pdf-meta',
    $year
    . ($filterPurpose ? ' &nbsp;&bull;&nbsp; Purpose: ' . ($purposes[$filterPurpose] ?? $filterPurpose) : '')
    . ' &nbsp;&bull;&nbsp; Records: ' . $benefits->count()
)

@section('content')

@php
$grandTotal = $benefits->sum('amount');
@endphp

<table class="stats-wrap">
    <tr>
        <td style="width:50%">
            <div class="stat-label">Total Disbursed</div>
            <div class="stat-value">{{ $currencySymbol }} {{ number_format($grandTotal, 2) }}</div>
        </td>
        <td style="width:50%">
            <div class="stat-label">Number of Benefits</div>
            <div class="stat-value">{{ $benefits->count() }}</div>
        </td>
    </tr>
</table>

<table class="data">
    <thead>
        <tr>
            <th>Date</th>
            <th>Beneficiary</th>
            <th>Purpose</th>
            <th>Description</th>
            <th>Approved By</th>
            <th class="r">Amount ({{ $currencySymbol }})</th>
            <th>Recorded By</th>
        </tr>
    </thead>
    <tbody>
        @forelse($benefits as $benefit)
        <tr>
            <td class="c">{{ \Carbon\Carbon::parse($benefit->benefit_date)->format('d M Y') }}</td>
            <td class="fw-b">{{ $benefit->beneficiary_name }}</td>
            <td>{{ $purposes[$benefit->purpose] ?? ucfirst($benefit->purpose) }}</td>
            <td>{{ $benefit->description ?: '—' }}</td>
            <td>{{ $benefit->approved_by ?: '—' }}</td>
            <td class="r">{{ number_format($benefit->amount, 2) }}</td>
            <td>{{ $benefit->recordedBy?->name ?? '—' }}</td>
        </tr>
        @empty
        <tr class="empty"><td colspan="7">No benefits found for the selected period.</td></tr>
        @endforelse
        @if($benefits->count())
        <tr class="total">
            <td colspan="5" class="r">Grand Total</td>
            <td class="r">{{ number_format($grandTotal, 2) }}</td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>
@endsection
