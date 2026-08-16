@extends('admin.pdf.ministry-layout')

@section('pdf-title', 'Welfare Balances — ' . $year)
@section('pdf-meta',
    $year . ' &nbsp;&bull;&nbsp; Status: ' . ucfirst($status)
    . ' &nbsp;&bull;&nbsp; Members: ' . $balances->count()
)

@section('content')

@php
$totalExpected = $balances->sum('expected');
$totalPaid     = $balances->sum('paid');
$totalBalance  = $balances->sum('balance');
$inArrears     = $balances->filter(fn($b) => $b['balance'] > 0)->count();
$fullyPaid     = $balances->filter(fn($b) => $b['balance'] == 0)->count();
$inCredit      = $balances->filter(fn($b) => $b['balance'] < 0)->count();
@endphp

<table class="stats-wrap">
    <tr>
        <td>
            <div class="stat-label">Total Expected</div>
            <div class="stat-value">{{ $currencySymbol }} {{ number_format($totalExpected, 2) }}</div>
        </td>
        <td>
            <div class="stat-label">Total Paid</div>
            <div class="stat-value pos">{{ $currencySymbol }} {{ number_format($totalPaid, 2) }}</div>
        </td>
        <td>
            <div class="stat-label">Outstanding Balance</div>
            <div class="stat-value {{ $totalBalance > 0 ? 'neg' : 'pos' }}">
                {{ $currencySymbol }} {{ number_format(abs($totalBalance), 2) }}
            </div>
        </td>
        <td>
            <div class="stat-label">In Arrears / Paid / Credit</div>
            <div class="stat-value">{{ $inArrears }} / {{ $fullyPaid }} / {{ $inCredit }}</div>
        </td>
    </tr>
</table>

<table class="data">
    <thead>
        <tr>
            <th>#</th>
            <th>Member</th>
            <th class="r">Expected ({{ $currencySymbol }})</th>
            <th class="r">Paid ({{ $currencySymbol }})</th>
            <th class="r">Balance ({{ $currencySymbol }})</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($balances as $i => $b)
        <tr>
            <td class="c text-mut">{{ $i + 1 }}</td>
            <td class="fw-b">{{ $b['member']->full_name }}</td>
            <td class="r">{{ number_format($b['expected'], 2) }}</td>
            <td class="r">{{ number_format($b['paid'], 2) }}</td>
            <td class="r {{ $b['balance'] > 0 ? 'text-neg' : ($b['balance'] < 0 ? 'text-pos' : '') }}">
                {{ number_format(abs($b['balance']), 2) }}
                @if($b['balance'] < 0) <span class="text-mut">(cr)</span>@endif
            </td>
            <td class="c">
                @if($b['balance'] > 0)
                    <span class="badge badge-r">Arrears</span>
                @elseif($b['balance'] < 0)
                    <span class="badge badge-b">Credit</span>
                @else
                    <span class="badge badge-g">Paid</span>
                @endif
            </td>
        </tr>
        @empty
        <tr class="empty"><td colspan="6">No members found.</td></tr>
        @endforelse
        @if($balances->count())
        <tr class="total">
            <td colspan="2" class="r">Totals</td>
            <td class="r">{{ number_format($totalExpected, 2) }}</td>
            <td class="r">{{ number_format($totalPaid, 2) }}</td>
            <td class="r {{ $totalBalance > 0 ? 'text-neg' : 'text-pos' }}">{{ number_format(abs($totalBalance), 2) }}</td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>
@endsection
