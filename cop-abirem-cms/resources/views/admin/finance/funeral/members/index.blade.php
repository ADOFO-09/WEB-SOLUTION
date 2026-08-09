@extends('layouts.admin')

@section('title', 'Member Funeral Summary')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:#1e3a5f;">Member Funeral Summary</h1>
        <p style="color:#64748b;margin-top:0.25rem;">View contribution history, balances, and support received for each member</p>
    </div>
</div>

<!-- Search -->
<form method="GET" style="background:#fff;border-radius:10px;padding:1rem 1.25rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);margin-bottom:1.25rem;">
    <div style="display:flex;gap:0.75rem;align-items:center;">
        <div style="flex:1;position:relative;">
            <svg style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or member ID..."
                   style="width:100%;padding:0.5rem 0.75rem 0.5rem 2.25rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;box-sizing:border-box;" autofocus>
        </div>
        <button type="submit" style="background:#1e3a5f;color:#fff;padding:0.5rem 1.25rem;border-radius:6px;border:none;font-size:0.875rem;font-weight:600;cursor:pointer;white-space:nowrap;">
            Search
        </button>
        @if($search)
        <a href="{{ route('admin.funeral.members.index') }}" style="font-size:0.82rem;color:#64748b;white-space:nowrap;">Clear</a>
        @endif
    </div>
</form>

<!-- Results Table -->
<div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
    <div style="overflow-x:auto;">
        <table style="width:100%;font-size:0.82rem;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                    <th style="text-align:left;padding:0.7rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Member</th>
                    <th style="text-align:left;padding:0.7rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Enrolled Since</th>
                    <th style="text-align:right;padding:0.7rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Contributions</th>
                    <th style="text-align:right;padding:0.7rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Total Paid</th>
                    <th style="text-align:right;padding:0.7rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Support Received</th>
                    <th style="padding:0.7rem 1rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                <tr style="border-bottom:1px solid #f8fafc;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <td style="padding:0.65rem 1rem;">
                        <div style="font-weight:600;color:#1e293b;">{{ $member->first_name }} {{ $member->last_name }}</div>
                        <div style="font-size:0.72rem;color:#94a3b8;">{{ $member->member_id }}</div>
                    </td>
                    <td style="padding:0.65rem 1rem;color:#64748b;font-size:0.82rem;">
                        @if($member->funeral_enrolled && $member->funeral_start_date)
                            {{ $member->funeral_start_date->format('d M Y') }}
                        @elseif($member->funeral_enrolled)
                            <span style="color:#64748b;">Enrolled</span>
                        @else
                            <span style="color:#94a3b8;font-style:italic;">Not enrolled</span>
                        @endif
                    </td>
                    <td style="padding:0.65rem 1rem;text-align:right;font-weight:600;color:#374151;">{{ $member->funeral_contributions_count }}</td>
                    <td style="padding:0.65rem 1rem;text-align:right;font-weight:700;color:#166534;">{{ $currencySymbol }} {{ number_format($member->total_paid ?? 0, 2) }}</td>
                    <td style="padding:0.65rem 1rem;text-align:right;font-weight:600;color:#7c3aed;">{{ $member->funeral_benefits_count }}</td>
                    <td style="padding:0.65rem 1rem;text-align:right;">
                        <a href="{{ route('admin.funeral.members.show', $member) }}"
                           style="display:inline-flex;align-items:center;gap:0.3rem;background:#1e3a5f;color:#fff;padding:0.3rem 0.75rem;border-radius:5px;font-size:0.75rem;font-weight:600;text-decoration:none;">
                            View Records
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:3.5rem;text-align:center;color:#9ca3af;font-size:0.875rem;">
                        @if($search)
                            No members found matching <strong>"{{ $search }}"</strong>. Try searching by full name or member ID.
                        @else
                            No funeral fund members found.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($members->hasPages())
<div style="margin-top:1rem;">{{ $members->links() }}</div>
@endif
@endsection
