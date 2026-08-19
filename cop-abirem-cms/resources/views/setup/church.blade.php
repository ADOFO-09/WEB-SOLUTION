@extends('setup.layout')
@section('title', 'Step 2 — Church Identity')

@section('content')
<div class="card">
    <div class="card-head">
        <h2>Step 2 — Church Identity</h2>
        <p>This information appears on receipts, SMS sign-offs, the login page, and printed reports.</p>
    </div>
    <div class="card-body">

        @if($errors->any())
        <div class="alert alert-error">
            <ul style="list-style: inside; padding-left: 0.5rem;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('setup.church.save') }}" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="grid-2">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Church / Organisation Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="church_name"
                           value="{{ old('church_name', $current['church_name'] !== 'My Church' ? $current['church_name'] : '') }}"
                           placeholder="e.g. Church of Pentecost — Abirem Assembly" required
                           class="{{ $errors->has('church_name') ? 'field-error' : '' }}">
                    @error('church_name')<p class="input-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Short Name <span class="opt">(optional)</span></label>
                    <input type="text" name="church_short_name"
                           value="{{ old('church_short_name', $current['church_short_name']) }}"
                           placeholder="e.g. COP Abirem" maxlength="60">
                    <p class="input-hint">Used in SMS sign-offs and compact displays.</p>
                </div>

                <div class="form-group">
                    <label>Slogan / Tagline <span class="opt">(optional)</span></label>
                    <input type="text" name="church_slogan"
                           value="{{ old('church_slogan', $current['church_slogan']) }}"
                           placeholder="e.g. Reaching the Unreached" maxlength="255">
                    <p class="input-hint">Shown below the church name on the login page.</p>
                </div>

                <div class="form-group">
                    <label>Phone Number <span class="opt">(optional)</span></label>
                    <input type="text" name="church_phone"
                           value="{{ old('church_phone', $current['church_phone']) }}"
                           placeholder="e.g. 0241234567" maxlength="30">
                </div>

                <div class="form-group">
                    <label>Email Address <span class="opt">(optional)</span></label>
                    <input type="email" name="church_email"
                           value="{{ old('church_email', $current['church_email']) }}"
                           placeholder="e.g. info@yourchurch.org" maxlength="150">
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Address <span class="opt">(optional)</span></label>
                    <textarea name="church_address" rows="2" placeholder="e.g. Abirem, Eastern Region, Ghana" maxlength="500">{{ old('church_address', $current['church_address']) }}</textarea>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Church Logo <span class="opt">(optional — PNG, JPG or SVG, max 2 MB)</span></label>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div class="logo-preview" id="logoPreview">
                            <span style="padding: 0.5rem;">No logo</span>
                        </div>
                        <div>
                            <input type="file" name="church_logo" id="logoInput" accept="image/*"
                                   onchange="previewLogo(this)"
                                   style="font-size: 0.85rem;">
                            <p class="input-hint" style="margin-top: 0.3rem;">Appears on the login page and printed documents. You can change this later in Settings.</p>
                        </div>
                    </div>
                    @error('church_logo')<p class="input-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('setup.account') }}" class="btn btn-ghost">← Back</a>
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

<script>
function previewLogo(input) {
    const preview = document.getElementById('logoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Logo preview">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
