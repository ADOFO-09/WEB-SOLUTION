@extends('layouts.admin')

@section('title', 'Ministry SMS')

@section('content')
<div class="mb-6">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e3a5f;">{{ $ministry->name }} - Send SMS</h1>
            <p style="color: #64748b; margin-top: 0.25rem;">Send SMS to your ministry members</p>
        </div>
        <a href="{{ route('admin.ministry.dashboard') }}" class="btn btn-secondary">
            <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to Dashboard
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: 1.5rem;">
    <!-- Main Content -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Compose Message</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.ministry.send-sms') }}" method="POST">
                    @csrf
                    
                    <!-- Recipient Selection -->
                    <div class="form-group">
                        <label class="form-label">Recipients</label>
                        <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="radio" name="recipient_type" value="all" checked onchange="toggleRecipientList()" style="margin-right: 0.5rem;">
                                <span>All Members ({{ $members->count() }})</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="radio" name="recipient_type" value="selected" onchange="toggleRecipientList()" style="margin-right: 0.5rem;">
                                <span>Select Members</span>
                            </label>
                        </div>

                        <!-- Member Selection List (Hidden by default) -->
                        <div id="memberSelectionList" style="display: none; max-height: 300px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem;">
                            <div style="margin-bottom: 0.75rem;">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="selectAllMembers()">Select All</button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="deselectAllMembers()">Deselect All</button>
                            </div>
                            @foreach($members as $member)
                            <label style="display: flex; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9; cursor: pointer;">
                                <input type="checkbox" name="recipient_ids[]" value="{{ $member->id }}" class="member-checkbox" style="margin-right: 0.75rem;">
                                <div style="flex: 1;">
                                    <div style="font-weight: 500; font-size: 0.875rem;">{{ $member->first_name }} {{ $member->last_name }}</div>
                                    <div style="font-size: 0.75rem; color: #64748b;">{{ $member->phone_primary ?? 'No phone' }}</div>
                                </div>
                                @if($member->phone_primary)
                                <span class="badge badge-success" style="font-size: 0.625rem;">Has Phone</span>
                                @else
                                <span class="badge badge-secondary" style="font-size: 0.625rem;">No Phone</span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="form-group">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-input" rows="5" maxlength="160" required 
                            placeholder="Type your message here..." 
                            oninput="updateCharCount()"></textarea>
                        <div class="form-hint">
                            <span id="charCount">160</span> characters remaining
                        </div>
                    </div>

                    <!-- Quick Templates -->
                    <div class="form-group">
                        <label class="form-label">Quick Templates</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="insertTemplate('meeting')">
                                Meeting Reminder
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="insertTemplate('prayer')">
                                Prayer Request
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="insertTemplate('event')">
                                Event Notification
                            </button>
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                            Send SMS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- SMS Stats -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">SMS Info</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: #64748b;">Ministry Members</span>
                    <span style="font-weight: 600;">{{ $members->count() }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: #64748b;">With Phone Numbers</span>
                    <span style="font-weight: 600; color: #10b981;">{{ $members->filter(fn($m) => !empty($m->phone_primary))->count() }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b;">Without Phone</span>
                    <span style="font-weight: 600; color: #ef4444;">{{ $members->filter(fn($m) => empty($m->phone_primary))->count() }}</span>
                </div>
                
                <div style="margin-top: 1rem; padding: 0.75rem; background: #fef3c7; border-radius: 0.5rem;">
                    <p style="font-size: 0.75rem; color: #92400e;">
                        <strong>Note:</strong> SMS will only be sent to members with valid phone numbers.
                    </p>
                </div>
            </div>
        </div>

        <!-- Message Preview -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Message Preview</h3>
            </div>
            <div class="card-body">
                <div style="background: #f8fafc; border-radius: 1rem 1rem 1rem 0; padding: 1rem; position: relative;">
                    <p id="messagePreview" style="font-size: 0.875rem; color: #374151; min-height: 60px;">
                        Your message will appear here...
                    </p>
                    <div style="font-size: 0.625rem; color: #9ca3af; text-align: right; margin-top: 0.5rem;">
                        {{ \App\Helpers\SettingHelper::churchShortName() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRecipientList() {
    const selectedRadio = document.querySelector('input[name="recipient_type"]:checked');
    const list = document.getElementById('memberSelectionList');
    list.style.display = selectedRadio.value === 'selected' ? 'block' : 'none';
}

function selectAllMembers() {
    document.querySelectorAll('.member-checkbox').forEach(cb => cb.checked = true);
}

function deselectAllMembers() {
    document.querySelectorAll('.member-checkbox').forEach(cb => cb.checked = false);
}

function updateCharCount() {
    const textarea = document.querySelector('textarea[name="message"]');
    const remaining = 160 - textarea.value.length;
    document.getElementById('charCount').textContent = remaining;
    document.getElementById('messagePreview').textContent = textarea.value || 'Your message will appear here...';
}

function insertTemplate(type) {
    const textarea = document.querySelector('textarea[name="message"]');
    const templates = {
        'meeting': 'Dear member, you are reminded of our ministry meeting this week. Please plan to attend. God bless!',
        'prayer': 'Dear member, please join us in prayer for our ministry. Let us intercede together for God\'s guidance.',
        'event': 'Dear member, you are invited to our upcoming ministry event. More details will be shared soon. God bless!'
    };
    textarea.value = templates[type] || '';
    updateCharCount();
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateCharCount();
});
</script>

<style>
@media (max-width: 1024px) {
    div[style*="grid-template-columns: 1fr 350px"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection