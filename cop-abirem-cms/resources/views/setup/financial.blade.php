@extends('setup.layout')
@section('title', 'Step 3 — Financial Year')

@section('content')
<div class="card">
    <div class="card-head">
        <h2>Step 3 — Financial Year</h2>
        <p>Every tithe, offering, donation, and pledge must be linked to an active financial year. Set up the current one now.</p>
    </div>
    <div class="card-body">

        @if($existing && $existing->is_active)
        <div class="info-box">
            <strong>Active year already exists</strong>
            <strong>{{ $existing->name }}</strong> ({{ \Carbon\Carbon::parse($existing->start_date)->format('d M Y') }} – {{ \Carbon\Carbon::parse($existing->end_date)->format('d M Y') }}) is currently active.
            You can update it below or leave it as-is and click <em>Continue</em>.
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-error">
            <ul style="list-style: inside; padding-left: 0.5rem;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('setup.financial.save') }}" novalidate>
            @csrf

            <div class="form-group">
                <label>Financial Year Name <span style="color:#dc2626;">*</span></label>
                <input type="text" name="year_name"
                       value="{{ old('year_name', $existing?->name ?? 'FY ' . $defaultYear) }}"
                       placeholder="e.g. FY {{ $defaultYear }}" required
                       class="{{ $errors->has('year_name') ? 'field-error' : '' }}">
                <p class="input-hint">Used as a label in reports and financial records.</p>
                @error('year_name')<p class="input-error">{{ $message }}</p>@enderror
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Start Date <span style="color:#dc2626;">*</span></label>
                    <input type="date" name="start_date"
                           value="{{ old('start_date', $existing?->start_date ?? $defaultYear . '-01-01') }}" required
                           class="{{ $errors->has('start_date') ? 'field-error' : '' }}">
                    @error('start_date')<p class="input-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>End Date <span style="color:#dc2626;">*</span></label>
                    <input type="date" name="end_date"
                           value="{{ old('end_date', $existing?->end_date ?? $defaultYear . '-12-31') }}" required
                           class="{{ $errors->has('end_date') ? 'field-error' : '' }}">
                    @error('end_date')<p class="input-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('setup.church') }}" class="btn btn-ghost">← Back</a>
                <button type="submit" class="btn btn-primary">
                    Save &amp; Continue
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
