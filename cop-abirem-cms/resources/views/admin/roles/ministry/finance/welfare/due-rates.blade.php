@extends('layouts.admin')

@section('title', 'Ministry Welfare Due Rates')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:#1e3a5f;">Welfare Due Rates</h1>
        <p style="color:#64748b;margin-top:0.25rem;">{{ $ministry->name }}</p>
    </div>
    <a href="{{ route('admin.ministry.finance.welfare.contributions') }}" style="font-size:0.85rem;color:#64748b;">&larr; Contributions</a>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;">{{ session('error') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

    <!-- Rate History -->
    <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <div style="padding:1.1rem 1.5rem;border-bottom:1px solid #f1f5f9;">
            <h2 style="font-size:1rem;font-weight:600;color:#1e3a5f;">Rate History</h2>
            <p style="font-size:0.75rem;color:#94a3b8;margin-top:0.2rem;">Historical rates are preserved for accurate balance calculations.</p>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;font-size:0.82rem;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="text-align:left;padding:0.6rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Effective From</th>
                        <th style="text-align:right;padding:0.6rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Amount / Month</th>
                        <th style="text-align:left;padding:0.6rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Notes</th>
                        <th style="text-align:left;padding:0.6rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Added By</th>
                        <th style="text-align:left;padding:0.6rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rates as $rate)
                    <tr style="border-bottom:1px solid #f8fafc;{{ $rate->is_current ? 'background:#f0fdf4;' : '' }}">
                        <td style="padding:0.65rem 1rem;color:#1e293b;font-weight:500;">
                            {{ $rate->effective_from->format('M d, Y') }}
                        </td>
                        <td style="padding:0.65rem 1rem;color:#166534;font-weight:700;text-align:right;">{{ $currencySymbol }} {{ number_format($rate->amount,2) }}</td>
                        <td style="padding:0.65rem 1rem;color:#64748b;">{{ $rate->notes ?? '—' }}</td>
                        <td style="padding:0.65rem 1rem;color:#64748b;">{{ $rate->createdBy?->name ?? '—' }}</td>
                        <td style="padding:0.65rem 1rem;">
                            @if($rate->is_current)
                            <span style="background:#dcfce7;color:#166534;font-size:0.68rem;font-weight:700;padding:0.2rem 0.55rem;border-radius:4px;">&#10003; Current</span>
                            @else
                            <form action="{{ route('admin.ministry.finance.welfare.due-rates.set-current', $rate->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Set this rate as the current active rate?')"
                                        style="background:none;border:none;color:#4f46e5;font-size:0.75rem;cursor:pointer;padding:0;text-decoration:underline;">
                                    Set as Current
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:2.5rem;text-align:center;color:#9ca3af;font-size:0.875rem;">No rates configured yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add New Rate -->
    <div style="background:#fff;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <div style="padding:1.1rem 1.5rem;border-bottom:1px solid #f1f5f9;">
            <h2 style="font-size:1rem;font-weight:600;color:#1e3a5f;">Add New Rate</h2>
        </div>
        <form method="POST" action="{{ route('admin.ministry.finance.welfare.due-rates.store') }}" style="padding:1.5rem;">
            @csrf
            <div style="display:grid;gap:1rem;">
                <div>
                    <label style="font-size:0.82rem;font-weight:500;color:#374151;">Amount per Month ({{ $currencySymbol }}) <span style="color:#dc2626;">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0.01" required
                           style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;box-sizing:border-box;">
                    @error('amount')<p style="color:#dc2626;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label style="font-size:0.82rem;font-weight:500;color:#374151;">Effective From <span style="color:#dc2626;">*</span></label>
                    <input type="date" name="effective_from" value="{{ old('effective_from') }}" required
                           style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;box-sizing:border-box;">
                    @error('effective_from')<p style="color:#dc2626;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label style="font-size:0.82rem;font-weight:500;color:#374151;">Notes</label>
                    <textarea name="notes" rows="3"
                              style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;resize:vertical;box-sizing:border-box;"
                              placeholder="e.g. Rate revised by ministry meeting">{{ old('notes') }}</textarea>
                </div>

                <div style="display:flex;align-items:flex-start;gap:0.5rem;padding:0.25rem 0;">
                    <input type="checkbox" name="is_current" id="ministry_is_current" value="1"
                           {{ old('is_current', '1') ? 'checked' : '' }}
                           style="margin-top:0.2rem;width:1rem;height:1rem;cursor:pointer;accent-color:#1e3a5f;">
                    <div>
                        <label for="ministry_is_current" style="font-size:0.82rem;font-weight:500;color:#374151;cursor:pointer;">Mark as current rate</label>
                        <p style="font-size:0.72rem;color:#94a3b8;margin-top:0.15rem;">Uncheck only when adding a historical or correction entry without changing the active rate.</p>
                    </div>
                </div>

                <button type="submit" style="background:#1e3a5f;color:#fff;padding:0.6rem 1.25rem;border-radius:6px;border:none;font-size:0.875rem;font-weight:600;cursor:pointer;width:100%;">
                    Add Rate
                </button>
            </div>
        </form>
        <div style="padding:0 1.5rem 1.25rem;font-size:0.72rem;color:#94a3b8;">
            Adding a new rate creates a new row. Old rates are preserved to calculate historical balances correctly.
        </div>
    </div>

</div>
@endsection
