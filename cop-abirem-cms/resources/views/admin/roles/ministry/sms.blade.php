@extends('layouts.admin')

@section('title', 'Ministry SMS')

@section('content')
<div class="mb-6">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e3a5f;">{{ $ministry->name }} &mdash; Send SMS</h1>
            <p style="color: #64748b; margin-top: 0.25rem;">Compose and send a message to your ministry</p>
        </div>
        <a href="{{ route('admin.ministry.dashboard') }}" class="btn btn-secondary">
            <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to Dashboard
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger mb-4">{{ session('error') }}</div>
@endif

<div style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">

    <!-- Compose Form -->
    <div class="card">
        <div class="card-header">
            <h3 style="font-weight: 600; color: #1e3a5f;">Compose Message</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.ministry.send-sms') }}" method="POST" id="smsForm">
                @csrf

                {{-- Recipients --}}
                <div class="form-group">
                    <label class="form-label">Recipients</label>
                    <div style="display: flex; gap: 1.5rem; margin-bottom: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                            <input type="radio" name="recipient_type" value="ministry" checked
                                   onchange="toggleRecipientFields()" style="accent-color: #1e3a5f;">
                            <span>By Ministry</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                            <input type="radio" name="recipient_type" value="custom"
                                   onchange="toggleRecipientFields()" style="accent-color: #1e3a5f;">
                            <span>Custom Numbers</span>
                        </label>
                    </div>

                    {{-- By Ministry: locked to leader's own ministry --}}
                    <div id="ministryField">
                        <select class="form-input" disabled style="background: #f8fafc; color: #374151; cursor: not-allowed;">
                            <option selected>{{ $ministry->name }}</option>
                        </select>
                        {{-- Hidden field carries the actual value --}}
                        <input type="hidden" name="ministry_id" value="{{ $ministry->id }}">
                        <p class="form-hint" style="margin-top: 0.4rem;">
                            Sending to <strong>{{ $memberCount }}</strong> {{ Str::plural('member', $memberCount) }} with a phone number.
                        </p>
                    </div>

                    {{-- Custom Numbers --}}
                    <div id="customField" style="display: none;">
                        <textarea name="custom_numbers" class="form-input @error('custom_numbers') is-invalid @enderror"
                                  rows="4"
                                  placeholder="Enter phone numbers separated by commas, semicolons, or new lines&#10;e.g. 0241234567, 0551234567">{{ old('custom_numbers') }}</textarea>
                        <p class="form-hint">One number per line, or separate with commas / semicolons.</p>
                        @error('custom_numbers')
                        <p class="text-danger" style="font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Template Picker --}}
                @if($templates->isNotEmpty())
                <div class="form-group">
                    <label class="form-label">Use a Template <span style="color: #94a3b8; font-weight: 400;">(optional)</span></label>
                    <select id="templatePicker" class="form-input" onchange="applyTemplate(this)">
                        <option value="">— Select a template —</option>
                        @foreach($templates as $tpl)
                        <option value="{{ $tpl->id }}"
                                data-content="{{ e($tpl->content) }}"
                                data-name="{{ e($tpl->name) }}">
                            {{ $tpl->name }}
                            @if($tpl->category) ({{ ucfirst($tpl->category) }}) @endif
                        </option>
                        @endforeach
                    </select>
                    <p class="form-hint">Selecting a template replaces the message below — you can still edit it.</p>
                </div>
                @endif

                {{-- Subject --}}
                <div class="form-group">
                    <label class="form-label">Subject <span style="color: #94a3b8; font-weight: 400;">(optional — for records only)</span></label>
                    <input type="text" name="subject" class="form-input" maxlength="255"
                           value="{{ old('subject') }}"
                           placeholder="{{ $ministry->name }} Ministry Message">
                </div>

                {{-- Message --}}
                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea name="message_content" id="messageContent"
                              class="form-input @error('message_content') is-invalid @enderror"
                              rows="6" maxlength="320" required
                              placeholder="Type your message here, or select a template above…"
                              oninput="updateCharCount()">{{ old('message_content') }}</textarea>
                    <div class="form-hint" style="display: flex; justify-content: space-between;">
                        <span>Supports: <code>{name}</code>, <code>{church_name}</code>, <code>{church_short_name}</code>, <code>{ministry_name}</code>, <code>{current_date}</code></span>
                        <span><span id="charCount">320</span> / 320</span>
                    </div>
                    @error('message_content')
                    <p class="text-danger" style="font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;" id="sendBtn">
                        <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                        Send SMS
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar -->
    <div style="display: flex; flex-direction: column; gap: 1rem;">

        <!-- Ministry Info -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Ministry Info</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: #64748b;">Ministry</span>
                    <span style="font-weight: 600;">{{ $ministry->name }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: #64748b;">Members with Phone</span>
                    <span style="font-weight: 600; color: #10b981;">{{ $memberCount }}</span>
                </div>
                <div style="margin-top: 1rem; padding: 0.75rem; background: #fef3c7; border-radius: 0.5rem;">
                    <p style="font-size: 0.75rem; color: #92400e; margin: 0;">
                        <strong>Note:</strong> SMS is sent only to members with a valid phone number.
                    </p>
                </div>
            </div>
        </div>

        <!-- Message Preview -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Preview</h3>
            </div>
            <div class="card-body">
                <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 0.75rem 0.75rem 0.75rem 0; padding: 1rem;">
                    <p id="messagePreview" style="font-size: 0.875rem; color: #374151; min-height: 60px; white-space: pre-wrap; word-break: break-word;">
                        Your message will appear here…
                    </p>
                    <div style="font-size: 0.7rem; color: #9ca3af; text-align: right; margin-top: 0.5rem; border-top: 1px solid #e0f2fe; padding-top: 0.4rem;">
                        {{ \App\Helpers\SettingHelper::churchShortName() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Placeholder Reference -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Placeholders</h3>
            </div>
            <div class="card-body" style="font-size: 0.8rem;">
                @foreach([
                    '{name}'              => "Recipient's name",
                    '{church_name}'       => 'Full church name',
                    '{church_short_name}' => 'Short church name',
                    '{ministry_name}'     => 'This ministry name',
                    '{current_date}'      => "Today's date",
                ] as $ph => $label)
                <div style="display: flex; justify-content: space-between; align-items: center;
                            padding: 0.35rem 0; border-bottom: 1px solid #f1f5f9; cursor: pointer;"
                     onclick="insertPlaceholder('{{ $ph }}')" title="Click to insert">
                    <code style="font-size: 0.75rem; background: #f1f5f9; padding: 0.1rem 0.4rem; border-radius: 0.25rem;">{{ $ph }}</code>
                    <span style="color: #64748b; font-size: 0.75rem;">{{ $label }}</span>
                </div>
                @endforeach
                <p style="color: #94a3b8; font-size: 0.72rem; margin-top: 0.5rem;">Click a placeholder to insert it at the cursor.</p>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRecipientFields() {
    const type = document.querySelector('input[name="recipient_type"]:checked')?.value;
    document.getElementById('ministryField').style.display = type === 'ministry' ? 'block' : 'none';
    document.getElementById('customField').style.display   = type === 'custom'   ? 'block' : 'none';
}

function updateCharCount() {
    const ta        = document.getElementById('messageContent');
    const remaining = 320 - ta.value.length;
    document.getElementById('charCount').textContent = remaining;
    document.getElementById('messagePreview').textContent = ta.value || 'Your message will appear here…';
}

function applyTemplate(sel) {
    const opt = sel.options[sel.selectedIndex];
    const content = opt.dataset.content || '';
    if (!content) return;
    const ta = document.getElementById('messageContent');
    ta.value = content;
    updateCharCount();
    // Reset picker so the same template can be re-applied
    setTimeout(() => sel.value = '', 0);
}

function insertPlaceholder(ph) {
    const ta    = document.getElementById('messageContent');
    const start = ta.selectionStart;
    const end   = ta.selectionEnd;
    ta.value = ta.value.slice(0, start) + ph + ta.value.slice(end);
    ta.selectionStart = ta.selectionEnd = start + ph.length;
    ta.focus();
    updateCharCount();
}

document.addEventListener('DOMContentLoaded', function () {
    updateCharCount();
    toggleRecipientFields();
});
</script>

<style>
@media (max-width: 1024px) {
    div[style*="grid-template-columns: 1fr 340px"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection
