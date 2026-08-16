@extends('layouts.admin')

@section('title', 'Ministry Welfare Benefits')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:#1e3a5f;">Welfare Benefits</h1>
        <p style="color:#64748b;margin-top:0.25rem;">{{ $ministry->name }}</p>
    </div>
    <div style="display:flex;gap:0.75rem;">
        <a href="{{ route('admin.ministry.finance.welfare.benefits.pdf', request()->only(['purpose','year'])) }}" class="btn btn-secondary" title="Export PDF">
            <svg style="width:1rem;height:1rem;margin-right:0.4rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
            Export PDF
        </a>
        <a href="{{ route('admin.ministry.finance.welfare.benefits.create') }}"
           style="background:#1e3a5f;color:#fff;padding:0.5rem 1.1rem;border-radius:8px;font-size:0.875rem;font-weight:600;text-decoration:none;">
            + Record Benefit
        </a>
        <a href="{{ route('admin.ministry.finance.index') }}" style="font-size:0.85rem;color:#64748b;padding:0.5rem 0;">&larr; Finance Overview</a>
    </div>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;">{{ session('error') }}</div>
@endif

<!-- Filters -->
<div style="background:#fff;border-radius:10px;padding:1rem 1.25rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);margin-bottom:1.25rem;display:flex;gap:1rem;flex-wrap:wrap;align-items:center;">
    <form method="GET" style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
        <select name="purpose" onchange="this.form.submit()" style="padding:0.4rem 0.7rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.8rem;">
            <option value="">All purposes</option>
            @foreach($purposes as $key => $label)
            <option value="{{ $key }}" {{ request('purpose') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="year" onchange="this.form.submit()" style="padding:0.4rem 0.7rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.8rem;">
            @foreach(range(now()->year, now()->year - 4) as $y)
            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>
</div>

<div style="background:#fff;border-radius:12px;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
    @if($benefits->isEmpty())
    <p style="color:#9ca3af;text-align:center;padding:3rem 0;font-size:0.875rem;">No welfare benefits recorded yet. <a href="{{ route('admin.ministry.finance.welfare.benefits.create') }}" style="color:#1e3a5f;font-weight:600;">Record one</a>.</p>
    @else
    <div style="overflow-x:auto;">
        <table style="width:100%;font-size:0.82rem;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid #f1f5f9;">
                    <th style="text-align:left;padding:0.6rem 0.75rem;color:#64748b;font-weight:600;">Beneficiary</th>
                    <th style="text-align:left;padding:0.6rem 0.75rem;color:#64748b;font-weight:600;">Purpose</th>
                    <th style="text-align:center;padding:0.6rem 0.75rem;color:#64748b;font-weight:600;">Date</th>
                    <th style="text-align:right;padding:0.6rem 0.75rem;color:#64748b;font-weight:600;">Amount</th>
                    <th style="text-align:right;padding:0.6rem 0.75rem;color:#64748b;font-weight:600;">Total Cost</th>
                    <th style="text-align:center;padding:0.6rem 0.75rem;color:#64748b;font-weight:600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($benefits as $benefit)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:0.6rem 0.75rem;">
                        <div style="font-weight:600;color:#1e293b;">{{ $benefit->beneficiary_name }}</div>
                        @if($benefit->member)
                        <div style="font-size:0.7rem;color:#94a3b8;">{{ $benefit->member->first_name }} {{ $benefit->member->last_name }}</div>
                        @endif
                    </td>
                    <td style="padding:0.6rem 0.75rem;">
                        <span style="background:#eff6ff;color:#1d4ed8;padding:0.2rem 0.55rem;border-radius:4px;font-size:0.72rem;font-weight:600;">
                            {{ $purposes[$benefit->purpose] ?? ucfirst($benefit->purpose) }}
                        </span>
                    </td>
                    <td style="padding:0.6rem 0.75rem;color:#475569;text-align:center;">{{ $benefit->benefit_date->format('d M Y') }}</td>
                    <td style="padding:0.6rem 0.75rem;color:#166534;font-weight:700;text-align:right;">{{ $currencySymbol }} {{ number_format($benefit->amount,2) }}</td>
                    <td style="padding:0.6rem 0.75rem;color:#7c3aed;font-weight:700;text-align:right;">{{ $currencySymbol }} {{ number_format($benefit->total_cost,2) }}</td>
                    <td style="padding:0.6rem 0.75rem;text-align:center;display:flex;gap:0.5rem;justify-content:center;">
                        <a href="{{ route('admin.ministry.finance.welfare.benefits.show', $benefit->id) }}"
                           style="color:#1e3a5f;font-size:0.78rem;font-weight:600;text-decoration:none;padding:0.25rem 0.6rem;border:1px solid #cbd5e1;border-radius:5px;">View</a>
                        <form method="POST" action="{{ route('admin.ministry.finance.welfare.benefits.delete', $benefit->id) }}"
                              onsubmit="return confirm('Delete this benefit record?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="color:#dc2626;font-size:0.78rem;font-weight:600;padding:0.25rem 0.6rem;border:1px solid #fca5a5;border-radius:5px;background:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem;">{{ $benefits->links() }}</div>
    @endif
</div>
@endsection
