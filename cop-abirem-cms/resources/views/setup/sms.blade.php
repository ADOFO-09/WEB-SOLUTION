@extends('setup.layout')
@section('title', 'Step 4 — SMS Gateway')

@section('content')
<div class="card">
    <div class="card-head">
        <h2>Step 4 — SMS Gateway <span style="font-size: 0.8rem; font-weight: 400; color: #94a3b8; margin-left: 0.5rem;">Optional</span></h2>
        <p>Kerith uses SMS to send tithe confirmations, birthday greetings, pledge reminders, and custom messages. You can skip this and configure it later in Settings → SMS.</p>
    </div>
    <div class="card-body">

        @if($errors->any())
        <div class="alert alert-error">
            <ul style="list-style: inside; padding-left: 0.5rem;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('setup.sms.save') }}" novalidate id="smsForm">
            @csrf
            <input type="hidden" name="skip_sms" id="skipFlag" value="0">

            <div class="form-group">
                <label>SMS Provider <span style="color:#dc2626;">*</span></label>
                <select name="sms_provider" class="{{ $errors->has('sms_provider') ? 'field-error' : '' }}">
                    <option value="">— Select provider —</option>
                    <option value="giantsms" {{ old('sms_provider') === 'giantsms' ? 'selected' : '' }}>GiantSMS</option>
                </select>
                <p class="input-hint">GiantSMS is the supported provider. More may be added in future versions.</p>
                @error('sms_provider')<p class="input-error">{{ $message }}</p>@enderror
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Sender ID <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="sms_sender_id"
                           value="{{ old('sms_sender_id') }}"
                           placeholder="e.g. COP Abirem" maxlength="11"
                           class="{{ $errors->has('sms_sender_id') ? 'field-error' : '' }}">
                    <p class="input-hint">Max 11 characters. Shown as the sender on recipients' phones.</p>
                    @error('sms_sender_id')<p class="input-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>API Key / Username <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="sms_api_key"
                           value="{{ old('sms_api_key') }}"
                           placeholder="GiantSMS username"
                           class="{{ $errors->has('sms_api_key') ? 'field-error' : '' }}">
                    @error('sms_api_key')<p class="input-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-actions" style="flex-wrap: wrap; gap: 0.75rem;">
                <a href="{{ route('setup.financial') }}" class="btn btn-ghost">← Back</a>
                <div style="display: flex; gap: 0.75rem; margin-left: auto;">
                    <button type="button" class="btn btn-secondary" onclick="skipSms()">
                        Skip for now
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Save &amp; Continue
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function skipSms() {
    document.getElementById('skipFlag').value = '1';
    document.getElementById('smsForm').submit();
}
</script>
@endsection
