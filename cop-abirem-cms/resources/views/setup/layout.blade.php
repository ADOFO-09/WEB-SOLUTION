<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Setup') — Kerith</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            color: #1e293b;
        }

        /* ── Top bar ── */
        .topbar {
            background: #1e3a5f;
            color: #fff;
            padding: 0.9rem 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .topbar-logo { font-size: 1.25rem; font-weight: 800; letter-spacing: 0.04em; }
        .topbar-sub  { font-size: 0.8rem; color: rgba(255,255,255,0.55); }

        /* ── Step progress bar ── */
        .steps-wrap {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.25rem 2rem;
        }
        .steps {
            display: flex;
            align-items: center;
            gap: 0;
            max-width: 720px;
            margin: 0 auto;
        }
        .step-item {
            display: flex;
            align-items: center;
            flex: 1;
        }
        .step-circle {
            width: 2rem; height: 2rem;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 700;
            flex-shrink: 0;
            border: 2px solid #cbd5e1;
            background: #f8fafc;
            color: #94a3b8;
            transition: all .2s;
        }
        .step-label {
            font-size: 0.72rem;
            color: #94a3b8;
            margin-left: 0.4rem;
            white-space: nowrap;
        }
        .step-connector {
            flex: 1;
            height: 2px;
            background: #e2e8f0;
            margin: 0 0.5rem;
        }
        .step-item.done   .step-circle { background: #10b981; border-color: #10b981; color: #fff; }
        .step-item.done   .step-label  { color: #10b981; }
        .step-item.done   .step-connector { background: #10b981; }
        .step-item.active .step-circle { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }
        .step-item.active .step-label  { color: #1e3a5f; font-weight: 600; }
        .step-item:last-child .step-connector { display: none; }

        /* ── Main card ── */
        .page-wrap {
            max-width: 720px;
            margin: 2.5rem auto;
            padding: 0 1rem;
        }
        .card {
            background: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 4px 16px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .card-head {
            padding: 1.5rem 2rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .card-head h2 { font-size: 1.25rem; font-weight: 700; color: #1e3a5f; }
        .card-head p  { font-size: 0.875rem; color: #64748b; margin-top: 0.25rem; }
        .card-body    { padding: 2rem; }

        /* ── Flash messages ── */
        .alert {
            padding: 0.85rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
        .alert-info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; }
        .alert-warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }

        /* ── Form elements ── */
        .form-group   { margin-bottom: 1.25rem; }
        label         { display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem; }
        label .opt    { font-weight: 400; color: #94a3b8; margin-left: 0.25rem; font-size: 0.8rem; }
        input[type="text"], input[type="email"], input[type="password"],
        input[type="date"], input[type="number"], select, textarea {
            width: 100%;
            padding: 0.6rem 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            color: #1e293b;
            background: #fff;
            transition: border-color .15s;
            outline: none;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #1e3a5f;
            box-shadow: 0 0 0 3px rgba(30,58,95,0.08);
        }
        .input-hint { font-size: 0.78rem; color: #94a3b8; margin-top: 0.3rem; }
        .input-error { font-size: 0.78rem; color: #dc2626; margin-top: 0.3rem; }
        .field-error { border-color: #dc2626 !important; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.6rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.875rem; font-weight: 600;
            cursor: pointer; border: none; transition: all .15s;
            text-decoration: none;
        }
        .btn-primary   { background: #1e3a5f; color: #fff; }
        .btn-primary:hover { background: #162d4a; }
        .btn-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-ghost     { background: transparent; color: #64748b; border: 1px solid #e2e8f0; }
        .btn-ghost:hover { background: #f8fafc; }
        .btn-success   { background: #10b981; color: #fff; }
        .btn-success:hover { background: #059669; }
        .form-actions {
            display: flex; justify-content: flex-end; gap: 0.75rem;
            margin-top: 2rem; padding-top: 1.5rem;
            border-top: 1px solid #f1f5f9;
        }

        /* ── Two-column grid ── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        @media (max-width: 560px) { .grid-2 { grid-template-columns: 1fr; } }

        /* ── Logo preview ── */
        .logo-preview {
            width: 80px; height: 80px;
            border: 2px dashed #e2e8f0;
            border-radius: 0.5rem;
            display: flex; align-items: center; justify-content: center;
            color: #cbd5e1; font-size: 0.75rem; text-align: center;
            overflow: hidden;
        }
        .logo-preview img { width: 100%; height: 100%; object-fit: contain; }

        /* ── Info box ── */
        .info-box {
            background: #f0f9ff; border: 1px solid #bae6fd;
            border-radius: 0.5rem; padding: 1rem;
            font-size: 0.85rem; color: #0369a1;
            margin-bottom: 1.5rem;
        }
        .info-box strong { display: block; margin-bottom: 0.25rem; }

        /* ── Skip link ── */
        .skip-row { text-align: center; margin-top: 1rem; }
        .skip-row button { background: none; border: none; color: #94a3b8; font-size: 0.8rem; cursor: pointer; text-decoration: underline; }
        .skip-row button:hover { color: #64748b; }
    </style>
</head>
<body>

<div class="topbar">
    <div>
        <div class="topbar-logo">Kerith</div>
        <div class="topbar-sub">First-Run Setup Wizard</div>
    </div>
</div>

@php
    $steps = [
        1 => ['label' => 'Account',   'slug' => 'account'],
        2 => ['label' => 'Church',    'slug' => 'church'],
        3 => ['label' => 'Finances',  'slug' => 'financial'],
        4 => ['label' => 'SMS',       'slug' => 'sms'],
        5 => ['label' => 'Done',      'slug' => 'complete'],
    ];
    $currentStep = $step ?? 0;
    $doneSteps   = collect($done ?? [])->flip();
@endphp

<div class="steps-wrap">
    <div class="steps">
        @foreach($steps as $n => $info)
        @php
            $isDone   = $doneSteps->has($info['slug']) || ($n === 5 && $currentStep === 5);
            $isActive = $n === $currentStep;
            $cls      = $isDone ? 'done' : ($isActive ? 'active' : '');
        @endphp
        <div class="step-item {{ $cls }}">
            <div class="step-circle">
                @if($isDone)
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                @else
                    {{ $n }}
                @endif
            </div>
            <span class="step-label">{{ $info['label'] }}</span>
            <div class="step-connector"></div>
        </div>
        @endforeach
    </div>
</div>

<div class="page-wrap">

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if(session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if(session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    @yield('content')
</div>

</body>
</html>
