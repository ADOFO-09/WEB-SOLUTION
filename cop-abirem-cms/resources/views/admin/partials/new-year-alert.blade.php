@if(isset($newYearSetupNeeded) && $newYearSetupNeeded && auth()->check() && auth()->user()->can('finance.manage'))
<div style="background:linear-gradient(135deg,#7f1d1d 0%,#b91c1c 100%);color:#fff;padding:1rem 1.5rem;border-left:5px solid #fca5a5;border-radius:8px;margin-bottom:1.25rem;box-shadow:0 2px 8px rgba(153,27,27,0.35);">
    <div style="display:flex;align-items:center;gap:1.1rem;flex-wrap:wrap;">

        <div style="flex-shrink:0;">
            <svg style="width:28px;height:28px;color:#fca5a5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <div style="flex:1;min-width:0;">
            <div style="font-size:0.92rem;font-weight:700;margin-bottom:0.2rem;letter-spacing:0.01em;">
                Action Required &mdash; No Financial Year Covers Today ({{ now()->format('d M Y') }})
            </div>
            <div style="font-size:0.8rem;color:#fecaca;line-height:1.55;">
                All tithes, offerings, donations and pledges must be linked to an active financial year.
                The system cannot properly record or report transactions until you set one up.
            </div>
        </div>

        <a href="{{ route('admin.finance.years.create', ['prefill_year' => now()->year]) }}"
           style="flex-shrink:0;display:inline-flex;align-items:center;gap:0.5rem;background:#fff;color:#991b1b;padding:0.55rem 1.1rem;border-radius:6px;font-size:0.82rem;font-weight:700;text-decoration:none;white-space:nowrap;box-shadow:0 1px 3px rgba(0,0,0,0.15);transition:opacity 0.15s;"
           onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Set Up {{ now()->year }} Financial Year
        </a>

    </div>
</div>
@endif
