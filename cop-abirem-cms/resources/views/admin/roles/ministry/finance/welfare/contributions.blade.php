@extends('layouts.admin')

@section('title', 'Ministry Welfare Contributions')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:#1e3a5f;">Welfare Contributions</h1>
        <p style="color:#64748b;margin-top:0.25rem;">{{ $ministry->name }} &mdash; {{ $year }}</p>
    </div>
    <a href="{{ route('admin.ministry.finance.index') }}" style="font-size:0.85rem;color:#64748b;">&larr; Finance Overview</a>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;">{{ session('error') }}</div>
@endif

<!-- Stats Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem;">
    <div style="background:#fff;border-radius:10px;padding:1rem 1.25rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);border-left:4px solid #22c55e;">
        <div style="font-size:0.72rem;text-transform:uppercase;color:#64748b;letter-spacing:0.06em;">Total Contributions {{ $year }}</div>
        <div style="font-size:1.5rem;font-weight:700;color:#166534;margin-top:0.25rem;">{{ $currencySymbol }} {{ number_format($stats['total_this_year'],2) }}</div>
    </div>
    <div style="background:#fff;border-radius:10px;padding:1rem 1.25rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);border-left:4px solid #3b82f6;">
        <div style="font-size:0.72rem;text-transform:uppercase;color:#64748b;letter-spacing:0.06em;">This Month</div>
        <div style="font-size:1.5rem;font-weight:700;color:#1e40af;margin-top:0.25rem;">{{ $stats['this_month_count'] }} records</div>
    </div>
    <div style="background:#fff;border-radius:10px;padding:1rem 1.25rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);border-left:4px solid #f59e0b;">
        <div style="font-size:0.72rem;text-transform:uppercase;color:#64748b;letter-spacing:0.06em;">Members Contributing</div>
        <div style="font-size:1.5rem;font-weight:700;color:#92400e;margin-top:0.25rem;">{{ $stats['unique_members'] }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

    <!-- Record Contribution Form -->
    <div style="background:#fff;border-radius:12px;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <h2 style="font-size:1rem;font-weight:600;color:#1e3a5f;margin-bottom:1rem;">Record Contribution</h2>
        <form method="POST" action="{{ route('admin.ministry.finance.welfare.contributions.store') }}">
            @csrf
            <div style="display:grid;gap:0.85rem;">
                <div>
                    <label style="font-size:0.8rem;font-weight:500;color:#374151;">Member <span style="color:#dc2626;">*</span></label>
                    <select name="member_id" required style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;">
                        <option value="">Select member...</option>
                        @foreach($members as $member)
                        <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                            {{ $member->first_name }} {{ $member->last_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('member_id')<p style="color:#dc2626;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                    <div>
                        <label style="font-size:0.8rem;font-weight:500;color:#374151;">Month <span style="color:#dc2626;">*</span></label>
                        <select name="period_month" required style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;">
                            @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ (old('period_month', now()->month) == $m) ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.8rem;font-weight:500;color:#374151;">Year <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="period_year" value="{{ old('period_year', now()->year) }}" min="2000" max="2099" required
                               style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;">
                    </div>
                </div>

                <div>
                    <label style="font-size:0.8rem;font-weight:500;color:#374151;">Amount ({{ $currencySymbol }}) <span style="color:#dc2626;">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0.01" required
                           style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;">
                    @error('amount')<p style="color:#dc2626;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label style="font-size:0.8rem;font-weight:500;color:#374151;">Payment Date <span style="color:#dc2626;">*</span></label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" required
                           style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;">
                </div>

                <div>
                    <label style="font-size:0.8rem;font-weight:500;color:#374151;">Payment Method <span style="color:#dc2626;">*</span></label>
                    <select name="payment_method" required style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;">
                        @foreach($methods as $key => $label)
                        <option value="{{ $key }}" {{ old('payment_method','cash') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size:0.8rem;font-weight:500;color:#374151;">Notes</label>
                    <textarea name="notes" rows="2" style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;resize:vertical;">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" style="background:#1e3a5f;color:#fff;padding:0.6rem 1.25rem;border-radius:6px;border:none;font-size:0.875rem;font-weight:600;cursor:pointer;width:100%;">
                    Record Contribution
                </button>
            </div>
        </form>
    </div>

    <!-- Contributions List -->
    <div style="background:#fff;border-radius:12px;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <h2 style="font-size:1rem;font-weight:600;color:#1e3a5f;">Contributions</h2>
            <form method="GET" style="display:flex;gap:0.5rem;align-items:center;">
                <select name="period_year" onchange="this.form.submit()" style="padding:0.35rem 0.6rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.8rem;">
                    @foreach(range(now()->year, now()->year - 4) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <select name="period_month" onchange="this.form.submit()" style="padding:0.35rem 0.6rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.8rem;">
                    <option value="">All months</option>
                    @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ request('period_month') == $m ? 'selected' : '' }}>{{ date('M', mktime(0,0,0,$m,1)) }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($contributions->isEmpty())
        <p style="color:#9ca3af;text-align:center;padding:2rem 0;font-size:0.875rem;">No contributions recorded for the selected period.</p>
        @else
        <div style="overflow-x:auto;">
            <table style="width:100%;font-size:0.8rem;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:2px solid #f1f5f9;">
                        <th style="text-align:left;padding:0.5rem 0.5rem;color:#64748b;font-weight:600;">Member</th>
                        <th style="text-align:center;padding:0.5rem 0.5rem;color:#64748b;font-weight:600;">Period</th>
                        <th style="text-align:right;padding:0.5rem 0.5rem;color:#64748b;font-weight:600;">Amount</th>
                        <th style="text-align:center;padding:0.5rem 0.5rem;color:#64748b;font-weight:600;">Method</th>
                        <th style="text-align:center;padding:0.5rem 0.5rem;color:#64748b;font-weight:600;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contributions as $contribution)
                    <tr style="border-bottom:1px solid #f8fafc;">
                        <td style="padding:0.5rem 0.5rem;color:#1e293b;font-weight:500;">
                            {{ $contribution->member?->first_name }} {{ $contribution->member?->last_name }}
                            <div style="font-size:0.7rem;color:#94a3b8;">{{ $contribution->reference_number }}</div>
                        </td>
                        <td style="padding:0.5rem 0.5rem;color:#475569;text-align:center;">
                            {{ date('M', mktime(0,0,0,$contribution->period_month,1)) }} {{ $contribution->period_year }}
                        </td>
                        <td style="padding:0.5rem 0.5rem;color:#166534;font-weight:700;text-align:right;">{{ $currencySymbol }} {{ number_format($contribution->amount,2) }}</td>
                        <td style="padding:0.5rem 0.5rem;color:#475569;text-align:center;text-transform:capitalize;">{{ str_replace('_',' ',$contribution->payment_method) }}</td>
                        <td style="padding:0.5rem 0.5rem;text-align:center;">
                            <form method="POST" action="{{ route('admin.ministry.finance.welfare.contributions.delete', $contribution->id) }}"
                                  onsubmit="return confirm('Delete this contribution?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="color:#dc2626;background:none;border:none;cursor:pointer;font-size:0.8rem;" title="Delete">&times;</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $contributions->links() }}</div>
        @endif
    </div>

</div>
@endsection
