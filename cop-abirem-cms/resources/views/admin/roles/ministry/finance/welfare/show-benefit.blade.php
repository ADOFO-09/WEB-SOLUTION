@extends('layouts.admin')

@section('title', 'Welfare Benefit Details')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:#1e3a5f;">Welfare Benefit</h1>
        <p style="color:#64748b;margin-top:0.25rem;">{{ $ministry->name }}</p>
    </div>
    <a href="{{ route('admin.ministry.finance.welfare.benefits') }}" style="font-size:0.85rem;color:#64748b;">&larr; Back to Benefits</a>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;">{{ session('success') }}</div>
@endif

<div style="max-width:700px;display:grid;gap:1.25rem;">

    <!-- Benefit Summary -->
    <div style="background:#fff;border-radius:12px;padding:1.75rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.25rem;">
            <div>
                <h2 style="font-size:1.1rem;font-weight:700;color:#1e293b;">{{ $benefit->beneficiary_name }}</h2>
                @if($benefit->member)
                <p style="font-size:0.82rem;color:#64748b;margin-top:0.15rem;">
                    Member: {{ $benefit->member->first_name }} {{ $benefit->member->last_name }}
                </p>
                @endif
            </div>
            <span style="background:#eff6ff;color:#1d4ed8;padding:0.3rem 0.75rem;border-radius:6px;font-size:0.8rem;font-weight:600;">
                {{ $purposes[$benefit->purpose] ?? ucfirst($benefit->purpose) }}
            </span>
        </div>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;background:#f8fafc;border-radius:8px;padding:1rem;margin-bottom:1.25rem;">
            <div>
                <div style="font-size:0.7rem;text-transform:uppercase;color:#94a3b8;letter-spacing:0.06em;">Benefit Date</div>
                <div style="font-size:1rem;font-weight:700;color:#1e293b;margin-top:0.2rem;">{{ $benefit->benefit_date->format('d M Y') }}</div>
            </div>
            <div>
                <div style="font-size:0.7rem;text-transform:uppercase;color:#94a3b8;letter-spacing:0.06em;">Amount Disbursed</div>
                <div style="font-size:1rem;font-weight:700;color:#166534;margin-top:0.2rem;">{{ $currencySymbol }} {{ number_format($benefit->amount,2) }}</div>
            </div>
            <div>
                <div style="font-size:0.7rem;text-transform:uppercase;color:#94a3b8;letter-spacing:0.06em;">Total Cost</div>
                <div style="font-size:1rem;font-weight:700;color:#7c3aed;margin-top:0.2rem;">{{ $currencySymbol }} {{ number_format($benefit->total_cost,2) }}</div>
            </div>
        </div>

        @if($benefit->description)
        <div style="background:#f8fafc;border-radius:8px;padding:0.85rem 1rem;font-size:0.85rem;color:#475569;margin-bottom:1rem;">
            {{ $benefit->description }}
        </div>
        @endif

        <div style="font-size:0.75rem;color:#94a3b8;border-top:1px solid #f1f5f9;padding-top:0.75rem;display:flex;gap:2rem;">
            <span>Recorded by: {{ $benefit->recordedBy?->name ?? 'System' }}</span>
            @if($benefit->approvedBy)
            <span>Approved by: {{ $benefit->approvedBy->name }}</span>
            @endif
        </div>
    </div>

    <!-- Expense Breakdown -->
    @if($benefit->expenses->isNotEmpty())
    <div style="background:#fff;border-radius:12px;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <h3 style="font-size:0.95rem;font-weight:600;color:#1e3a5f;margin-bottom:0.85rem;">Expense Breakdown</h3>
        <table style="width:100%;font-size:0.85rem;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid #f1f5f9;">
                    <th style="text-align:left;padding:0.5rem 0.75rem;color:#64748b;font-weight:600;">#</th>
                    <th style="text-align:left;padding:0.5rem 0.75rem;color:#64748b;font-weight:600;">Description</th>
                    <th style="text-align:right;padding:0.5rem 0.75rem;color:#64748b;font-weight:600;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($benefit->expenses as $i => $expense)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:0.5rem 0.75rem;color:#94a3b8;">{{ $i + 1 }}</td>
                    <td style="padding:0.5rem 0.75rem;color:#374151;">{{ $expense->description }}</td>
                    <td style="padding:0.5rem 0.75rem;color:#166534;font-weight:600;text-align:right;">{{ $currencySymbol }} {{ number_format($expense->amount,2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top:2px solid #e2e8f0;">
                    <td colspan="2" style="padding:0.6rem 0.75rem;font-weight:700;color:#374151;">Subtotal (Expenses)</td>
                    <td style="padding:0.6rem 0.75rem;font-weight:700;color:#7c3aed;text-align:right;">{{ $currencySymbol }} {{ number_format($benefit->total_expenses,2) }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="padding:0.3rem 0.75rem;font-weight:700;color:#374151;">Cash/Cheque Disbursed</td>
                    <td style="padding:0.3rem 0.75rem;font-weight:700;color:#166534;text-align:right;">{{ $currencySymbol }} {{ number_format($benefit->amount,2) }}</td>
                </tr>
                <tr style="background:#f0fdf4;">
                    <td colspan="2" style="padding:0.6rem 0.75rem;font-weight:700;color:#166534;">Grand Total</td>
                    <td style="padding:0.6rem 0.75rem;font-weight:700;color:#166534;text-align:right;">{{ $currencySymbol }} {{ number_format($benefit->total_cost,2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <!-- Actions -->
    <div style="display:flex;gap:0.75rem;">
        <a href="{{ route('admin.ministry.finance.welfare.benefits') }}"
           style="background:#f1f5f9;color:#475569;padding:0.6rem 1.25rem;border-radius:8px;font-size:0.875rem;font-weight:600;text-decoration:none;border:1px solid #e2e8f0;">
            &larr; All Benefits
        </a>
        <form method="POST" action="{{ route('admin.ministry.finance.welfare.benefits.delete', $benefit->id) }}"
              onsubmit="return confirm('Permanently delete this welfare benefit record?')">
            @csrf @method('DELETE')
            <button type="submit" style="background:#fee2e2;color:#dc2626;padding:0.6rem 1.25rem;border-radius:8px;border:1px solid #fca5a5;font-size:0.875rem;font-weight:600;cursor:pointer;">
                Delete Record
            </button>
        </form>
    </div>

</div>
@endsection
