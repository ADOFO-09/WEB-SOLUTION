@extends('setup.layout')
@section('title', 'Setup Complete')

@section('content')
<div class="card">
    <div class="card-head">
        <h2>Setup Complete</h2>
        <p>Kerith is ready. Here's a summary of what was configured.</p>
    </div>
    <div class="card-body">

        <div style="text-align: center; padding: 1rem 0 1.75rem;">
            <div style="width: 4rem; height: 4rem; background: #dcfce7; border-radius: 50%;
                        display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <svg width="32" height="32" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 700; color: #1e3a5f;">{{ $churchName }}</h3>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">is now live on Kerith</p>
        </div>

        <div style="background: #f8fafc; border-radius: 0.5rem; padding: 1.25rem; margin-bottom: 1.75rem;">
            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e2e8f0; font-size: 0.875rem;">
                <span style="color: #64748b;">Administrator account</span>
                <span style="color: #10b981; font-weight: 600;">✓ Secured</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e2e8f0; font-size: 0.875rem;">
                <span style="color: #64748b;">Church identity</span>
                <span style="color: #10b981; font-weight: 600;">✓ Configured</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e2e8f0; font-size: 0.875rem;">
                <span style="color: #64748b;">Financial year</span>
                <span style="color: #10b981; font-weight: 600;">✓ Active</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.875rem;">
                <span style="color: #64748b;">SMS gateway</span>
                @if($smsEnabled)
                <span style="color: #10b981; font-weight: 600;">✓ Enabled</span>
                @else
                <span style="color: #f59e0b; font-weight: 600;">⚠ Not configured yet</span>
                @endif
            </div>
        </div>

        @unless($smsEnabled)
        <div class="alert alert-warning" style="margin-bottom: 1.5rem;">
            SMS is not yet configured. Go to <strong>Settings → SMS</strong> when you're ready to enable it.
        </div>
        @endunless

        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.75rem; font-size: 0.85rem; color: #1d4ed8;">
            <strong style="display: block; margin-bottom: 0.5rem;">Recommended next steps</strong>
            <ul style="padding-left: 1.25rem; line-height: 1.85;">
                <li>Add your first member records under <strong>People → Members</strong></li>
                <li>Create member accounts for your secretary and finance team under <strong>System → Users</strong></li>
                <li>Review SMS templates under <strong>Communication → Templates</strong></li>
                @unless($smsEnabled)<li>Configure SMS credentials under <strong>Settings → SMS</strong></li>@endunless
                <li>Verify backup settings under <strong>Settings → Backup</strong></li>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-success" style="padding: 0.75rem 2rem; font-size: 1rem;">
                Go to Dashboard →
            </a>
        </div>

    </div>
</div>
@endsection
