@extends('admin.pdf.ministry-layout')

@section('pdf-title', 'Welfare Contributions — ' . $year)
@section('pdf-meta',
    $year
    . ($filterMember ? ' &nbsp;&bull;&nbsp; Member: ' . ($memberNames[$filterMember] ?? $filterMember) : '')
    . ($filterMonth  ? ' &nbsp;&bull;&nbsp; Month: ' . \Carbon\Carbon::create($year, $filterMonth, 1)->format('F') : '')
    . ' &nbsp;&bull;&nbsp; Records: ' . $contributions->count()
)

@section('content')

@php
$grandTotal = $contributions->sum('amount');
$uniqueMembers = $contributions->pluck('member_id')->unique()->count();
@endphp

<table class="stats-wrap">
    <tr>
        <td>
            <div class="stat-label">Total Collected</div>
            <div class="stat-value">{{ $currencySymbol }} {{ number_format($grandTotal, 2) }}</div>
        </td>
        <td>
            <div class="stat-label">Contributing Members</div>
            <div class="stat-value">{{ $uniqueMembers }}</div>
        </td>
        <td>
            <div class="stat-label">Total Records</div>
            <div class="stat-value">{{ $contributions->count() }}</div>
        </td>
    </tr>
</table>

<table class="data">
    <thead>
        <tr>
            <th>Date Paid</th>
            <th>Reference</th>
            <th>Member</th>
            <th class="c">Period</th>
            <th>Payment Method</th>
            <th class="r">Amount ({{ $currencySymbol }})</th>
            <th>Received By</th>
        </tr>
    </thead>
    <tbody>
        @forelse($contributions as $c)
        <tr>
            <td class="c">{{ \Carbon\Carbon::parse($c->payment_date)->format('d M Y') }}</td>
            <td>{{ $c->reference_number }}</td>
            <td>{{ $c->member?->full_name ?? '—' }}</td>
            <td class="c">{{ \Carbon\Carbon::create($c->period_year, $c->period_month, 1)->format('M Y') }}</td>
            <td class="c">{{ ucfirst(str_replace('_', ' ', $c->payment_method)) }}</td>
            <td class="r">{{ number_format($c->amount, 2) }}</td>
            <td>{{ $c->receivedBy?->name ?? '—' }}</td>
        </tr>
        @empty
        <tr class="empty"><td colspan="7">No contributions found for the selected period.</td></tr>
        @endforelse
        @if($contributions->count())
        <tr class="total">
            <td colspan="5" class="r">Grand Total</td>
            <td class="r">{{ number_format($grandTotal, 2) }}</td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>
@endsection
