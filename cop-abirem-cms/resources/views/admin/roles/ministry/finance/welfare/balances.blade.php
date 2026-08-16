@extends('layouts.admin')

@section('title', 'Ministry Welfare Balances')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:#1e3a5f;">Welfare Balances</h1>
        <p style="color:#64748b;margin-top:0.25rem;">{{ $ministry->name }} &mdash; {{ $year }}</p>
    </div>
    <div style="display:flex;gap:0.75rem;align-items:center;">
        <a href="{{ route('admin.ministry.finance.welfare.balances.pdf', request()->only(['year','status'])) }}" class="btn btn-secondary" title="Export PDF">
            <svg style="width:1rem;height:1rem;margin-right:0.4rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
            Export PDF
        </a>
        <a href="{{ route('admin.ministry.finance.welfare.fund-summary') }}" style="font-size:0.85rem;color:#64748b;">&larr; Fund Summary</a>
    </div>
</div>

<!-- Filters -->
<div style="background:#fff;border-radius:10px;padding:1rem 1.25rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);margin-bottom:1.25rem;">
    <form method="GET" style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap;">
        <div style="display:flex;gap:1.25rem;align-items:center;flex-wrap:wrap;">
            <label style="display:flex;align-items:center;gap:0.4rem;font-size:0.85rem;cursor:pointer;">
                <input type="radio" name="status" value="all" {{ $status === 'all' ? 'checked' : '' }} onchange="this.form.submit()">
                All Members
            </label>
            <label style="display:flex;align-items:center;gap:0.4rem;font-size:0.85rem;cursor:pointer;color:#dc2626;font-weight:500;">
                <input type="radio" name="status" value="arrears" {{ $status === 'arrears' ? 'checked' : '' }} onchange="this.form.submit()">
                In Arrears
            </label>
            <label style="display:flex;align-items:center;gap:0.4rem;font-size:0.85rem;cursor:pointer;color:#166534;font-weight:500;">
                <input type="radio" name="status" value="paid" {{ $status === 'paid' ? 'checked' : '' }} onchange="this.form.submit()">
                Fully Paid
            </label>
            <label style="display:flex;align-items:center;gap:0.4rem;font-size:0.85rem;cursor:pointer;color:#1d4ed8;font-weight:500;">
                <input type="radio" name="status" value="credit" {{ $status === 'credit' ? 'checked' : '' }} onchange="this.form.submit()">
                In Credit
            </label>
        </div>
        <input type="hidden" name="year" value="{{ $year }}">
        <select name="year" onchange="this.form.submit()" style="padding:0.35rem 0.6rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.8rem;margin-left:auto;">
            @foreach(range(now()->year, now()->year - 4) as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>
</div>

<div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
    <div style="overflow-x:auto;">
        <table style="width:100%;font-size:0.82rem;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                    <th style="text-align:left;padding:0.7rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Member</th>
                    <th style="text-align:right;padding:0.7rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Expected ({{ $year }})</th>
                    <th style="text-align:right;padding:0.7rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Paid</th>
                    <th style="text-align:right;padding:0.7rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Balance</th>
                    <th style="text-align:center;padding:0.7rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($balances as $row)
                @php
                    $bal      = $row['balance'];
                    $isArrear = $bal > 0;
                    $isCredit = $bal < 0;
                    $isPaid   = $bal == 0;
                @endphp
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:0.65rem 1rem;">
                        <div style="font-weight:600;color:#1e293b;">{{ $row['member']->first_name }} {{ $row['member']->last_name }}</div>
                        <div style="font-size:0.7rem;color:#94a3b8;">{{ $row['member']->member_id ?? '' }}</div>
                    </td>
                    <td style="padding:0.65rem 1rem;color:#374151;text-align:right;">
                        {{ $row['expected'] > 0 ? $currencySymbol.' '.number_format($row['expected'],2) : '—' }}
                    </td>
                    <td style="padding:0.65rem 1rem;color:#166534;font-weight:600;text-align:right;">{{ $currencySymbol }} {{ number_format($row['paid'],2) }}</td>
                    <td style="padding:0.65rem 1rem;font-weight:700;text-align:right;color:{{ $isArrear ? '#dc2626' : ($isCredit ? '#1d4ed8' : '#166534') }};">
                        {{ $isCredit ? '-' : '' }}{{ $currencySymbol }} {{ number_format(abs($bal),2) }}
                    </td>
                    <td style="padding:0.65rem 1rem;text-align:center;">
                        @if($isArrear)
                        <span style="background:#fee2e2;color:#991b1b;font-size:0.72rem;font-weight:700;padding:0.2rem 0.55rem;border-radius:12px;">Arrears</span>
                        @elseif($isCredit)
                        <span style="background:#eff6ff;color:#1d4ed8;font-size:0.72rem;font-weight:700;padding:0.2rem 0.55rem;border-radius:12px;">Credit</span>
                        @else
                        <span style="background:#dcfce7;color:#166534;font-size:0.72rem;font-weight:700;padding:0.2rem 0.55rem;border-radius:12px;">Paid</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:3rem;text-align:center;color:#9ca3af;font-size:0.875rem;">
                        @if($status === 'all')
                        No members found in this ministry.
                        @else
                        No members match the selected filter.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($balances->isEmpty() === false)
<p style="font-size:0.72rem;color:#94a3b8;margin-top:0.75rem;">
    Expected amounts are calculated from the ministry welfare due rates × 12 months for {{ $year }}.
    <a href="{{ route('admin.ministry.finance.welfare.due-rates') }}" style="color:#1e3a5f;">Manage rates</a>
</p>
@endif
@endsection
