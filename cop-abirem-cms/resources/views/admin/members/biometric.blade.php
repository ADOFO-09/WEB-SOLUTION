@extends('layouts.admin')

@section('title', 'Biometric Enrollment — ' . $member->full_name)

@section('header')
<div class="flex items-center justify-between">
    <div class="flex items-center">
        <a href="{{ route('admin.members.show', $member) }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Biometric Enrollment</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $member->full_name }} · {{ $member->member_id }}</p>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Enrollment Status Card --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Enrollment Status</h2>
            @if($member->biometric_enrolled)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Enrolled
            </span>
            @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                Not Enrolled
            </span>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4">
            {{-- Primary Finger --}}
            <div class="p-4 rounded-lg border-2 {{ $member->fingerprint_template_1 ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 {{ $member->fingerprint_template_1 ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                    </svg>
                    <span class="font-medium text-sm {{ $member->fingerprint_template_1 ? 'text-green-800' : 'text-gray-600' }}">
                        Primary Finger
                    </span>
                </div>
                @if($member->fingerprint_template_1)
                <p class="text-xs text-green-700">Template stored</p>
                @else
                <p class="text-xs text-gray-500">Not captured</p>
                @endif
            </div>

            {{-- Backup Finger --}}
            <div class="p-4 rounded-lg border-2 {{ $member->fingerprint_template_2 ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 {{ $member->fingerprint_template_2 ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                    </svg>
                    <span class="font-medium text-sm {{ $member->fingerprint_template_2 ? 'text-green-800' : 'text-gray-600' }}">
                        Backup Finger
                    </span>
                </div>
                @if($member->fingerprint_template_2)
                <p class="text-xs text-green-700">Template stored</p>
                @else
                <p class="text-xs text-gray-500">Not captured (optional)</p>
                @endif
            </div>
        </div>

        @if($member->biometric_enrolled_at)
        <p class="text-xs text-gray-400 mt-3">
            Last enrolled: {{ $member->biometric_enrolled_at->format('M d, Y g:i A') }}
        </p>
        @endif
    </div>

    {{-- Scanner Interface --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Capture Fingerprint</h2>
        <p class="text-sm text-gray-500 mb-5">Select a finger slot, then <strong>place the member's finger on the scanner</strong> and hold it still. The fingerprint will be captured and saved automatically.</p>

        {{-- Fingerprint visual area --}}
        <div id="scanner-visual" class="flex flex-col items-center justify-center py-10 px-6 rounded-lg border-2 border-dashed border-gray-300 mb-5 transition-colors duration-300">
            <div id="fp-icon" class="mb-4">
                <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                </svg>
            </div>
            <p id="scanner-status" class="text-base font-medium text-gray-500">Connecting to scanner…</p>
            <p id="scanner-sub" class="text-sm text-gray-400 mt-1">Please wait</p>
        </div>

        {{-- Finger selector --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-4">
            <select id="finger-select" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="1">Primary Finger (recommended)</option>
                <option value="2">Backup Finger (optional)</option>
            </select>
            {{-- Retry button — hidden until an error occurs --}}
            <button id="btn-retry" onclick="startCapture()" style="display:none"
                class="inline-flex items-center justify-center gap-2 px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Retry
            </button>
        </div>

        {{-- Bridge connection notice (replaced dynamically by JS) --}}
        <div id="test-mode-notice" class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700 mb-4">
            <strong>Connecting to scanner bridge…</strong> Please wait.
        </div>

        {{-- Not-installed notice (shown when bridge cannot connect) --}}
        <div id="bridge-not-installed" class="hidden mb-4 rounded-lg border border-blue-200 bg-blue-50 p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-blue-800">Scanner bridge not detected on this computer</p>
                    <p class="text-sm text-blue-700 mt-1">
                        The fingerprint scanner bridge needs to be installed once on this computer.
                        Download the setup package, extract it, and run <strong>install-service.bat</strong> as Administrator.
                        After that, it starts automatically every time this computer boots — no further action needed.
                    </p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <a href="{{ route('admin.members.biometric.download-bridge') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download Bridge Setup
                        </a>
                        <button onclick="retryBridgeConnect()"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-blue-300 text-blue-700 text-xs font-semibold rounded-lg hover:bg-blue-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            I've installed it — Retry
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Saving indicator (shown while saving) --}}
        <div id="saving-indicator" class="hidden p-3 bg-indigo-50 border border-indigo-200 rounded-lg text-sm text-indigo-700">
            <span class="inline-flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                Saving fingerprint…
            </span>
        </div>

        {{-- Error message --}}
        <div id="error-msg" class="hidden p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700"></div>
    </div>

    {{-- Remove Biometric Data --}}
    @if($member->biometric_enrolled)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Remove Biometric Data</h2>
        <p class="text-sm text-gray-500 mb-4">This will permanently delete all fingerprint templates and unenroll this member from biometric attendance.</p>
        <form action="{{ route('admin.members.biometric.remove', $member) }}" method="POST"
              onsubmit="return confirm('Remove all biometric data for {{ $member->full_name }}? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 border border-red-300 text-red-700 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Remove Biometric Data
            </button>
        </form>
    </div>
    @endif

</div>

@push('scripts')
{{-- Success toast --}}
<div id="success-toast"
     style="display:none;position:fixed;top:1.5rem;right:1.5rem;z-index:9999;min-width:18rem;"
     class="flex items-center gap-3 px-5 py-4 bg-green-600 text-white text-sm font-medium rounded-xl shadow-2xl transition-all duration-300">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span id="success-toast-msg">Fingerprint enrolled successfully!</span>
</div>

<script>
const ENROLL_URL            = "{{ route('admin.members.biometric.enroll', $member) }}";
const ENROLLED_TEMPLATES_URL = "{{ route('admin.members.biometric.enrolled-templates', $member) }}";
const CSRF_TOKEN            = "{{ csrf_token() }}";

let capturedTemplate = null;
let ws               = null;
let bridgeConnected  = false;
let saving           = false;
let otherMembers     = [];   // enrolled members (excluding this one) — for duplicate check
let usingIdentify    = false; // true when bridge is in start_identify mode

// ── Boot: load other members, then connect bridge ─────────────────────────
async function init() {
    try {
        const res  = await fetch(ENROLLED_TEMPLATES_URL, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        otherMembers = data.members || [];
    } catch (_) {
        otherMembers = [];
    }
    connectBridge();
}

// ── WebSocket bridge ───────────────────────────────────────────────────────
function connectBridge() {
    try {
        ws = new WebSocket('ws://localhost:15896/fingerprint');

        ws.onopen = () => {
            bridgeConnected = true;
            document.getElementById('test-mode-notice').style.display = 'none';
            document.getElementById('btn-retry').style.display = 'none';

            if (otherMembers.length > 0) {
                // Run identify first — catches duplicates AND returns the template on no-match
                usingIdentify = true;
                ws.send(JSON.stringify({
                    action: 'start_identify',
                    members: otherMembers.map(m => ({ id: m.id, t1: m.t1, t2: m.t2 || null }))
                }));
                setStatus('Place your finger on the scanner', 'Checking for duplicate fingerprints…', 'scan');
            } else {
                // No other enrolled members — straight capture
                usingIdentify = false;
                startCapture();
            }
        };

        ws.onclose = () => { bridgeConnected = false; ws = null; if (!saving) startSimulationMode(); };
        ws.onerror = () => { bridgeConnected = false; ws = null; startSimulationMode(); };

        ws.onmessage = (e) => {
            let d;
            try { d = JSON.parse(e.data); } catch (_) { return; }

            if (d.type === 'ready') {
                // Bridge loaded templates and is ready — wait for finger
                setStatus('Place your finger on the scanner',
                    `Checking against ${d.count} enrolled member fingerprint(s)…`, 'scan');

            } else if (d.type === 'identify_result') {
                if (d.matched) {
                    // Finger already belongs to another member
                    const match = otherMembers.find(m => m.id === d.member_id);
                    const name  = match ? match.name : 'another member';
                    onDuplicateDetected(name);
                } else if (d.template) {
                    // Recompiled bridge: returns template on no-match — save in one scan
                    onCaptureSuccess(d.template);
                } else {
                    // Current bridge: no template returned on no-match — stop identify mode
                    // and fall through to plain capture so the finger is actually stored
                    usingIdentify = false;
                    clearError();
                    setStatus('Fingerprint is unique!',
                        'Please place the same finger on the scanner once more to enroll it…', 'scan');
                    if (ws && ws.readyState === WebSocket.OPEN) {
                        ws.send(JSON.stringify({ action: 'stop_identify' }));
                    }
                    setTimeout(() => startCapture(), 400);
                }

            } else if (d.type === 'capture_result') {
                // Plain capture mode (no other enrolled members)
                if (d.success) {
                    onCaptureSuccess(d.template);
                } else {
                    onCaptureError(d.message || 'Capture failed. Try again.');
                }
            }
        };
    } catch (_) {
        startSimulationMode();
    }
}

// ── Clear error / saving UI ────────────────────────────────────────────────
function clearError() {
    document.getElementById('saving-indicator').classList.add('hidden');
    document.getElementById('error-msg').classList.add('hidden');
    document.getElementById('btn-retry').style.display = 'none';
}

// ── Start a plain capture ──────────────────────────────────────────────────
function startCapture() {
    if (saving) return;
    capturedTemplate = null;
    clearError();

    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({ action: 'capture' }));
        setStatus('Place your finger on the scanner', 'Hold still until captured', 'scan');
    }
}

// ── Retry after duplicate/error ────────────────────────────────────────────
function retryCapture() {
    if (saving) return;
    capturedTemplate = null;
    clearError();

    if (!ws || ws.readyState !== WebSocket.OPEN) return;

    if (usingIdentify) {
        // Re-enter identify mode so the next scan is checked for duplicates again
        ws.send(JSON.stringify({
            action: 'start_identify',
            members: otherMembers.map(m => ({ id: m.id, t1: m.t1, t2: m.t2 || null }))
        }));
        setStatus('Place your finger on the scanner', 'Checking for duplicate fingerprints…', 'scan');
    } else {
        startCapture();
    }
}

// ── Bridge not installed / unreachable ─────────────────────────────────────
function startSimulationMode() {
    document.getElementById('test-mode-notice').style.display = 'none';
    document.getElementById('bridge-not-installed').classList.remove('hidden');
    setStatus('Scanner bridge not running', 'Install the bridge on this computer to enable scanning', 'idle');
}

function retryBridgeConnect() {
    document.getElementById('bridge-not-installed').classList.add('hidden');
    document.getElementById('test-mode-notice').style.display = '';
    document.getElementById('test-mode-notice').innerHTML = '<strong>Reconnecting…</strong> Please wait.';
    setStatus('Connecting to scanner…', 'Please wait', 'idle');
    // Small delay then retry
    setTimeout(() => { ws = null; bridgeConnected = false; connectBridge(); }, 800);
}

// ── UI helpers ─────────────────────────────────────────────────────────────
function setStatus(text, sub, color) {
    document.getElementById('scanner-status').textContent = text;
    document.getElementById('scanner-sub').textContent = sub;
    const visual = document.getElementById('scanner-visual');
    visual.className = visual.className.replace(/border-\S+/g, '').replace(/bg-\S+/g, '');
    const colors = {
        idle:    'border-gray-300',
        scan:    'border-indigo-400 bg-indigo-50',
        success: 'border-green-400 bg-green-50',
        error:   'border-red-400 bg-red-50',
    };
    visual.classList.add('flex','flex-col','items-center','justify-center','py-10','px-6','rounded-lg','border-2','border-dashed','transition-colors','duration-300', ...(colors[color] || colors.idle).split(' '));
}

function showToast(msg) {
    const toast = document.getElementById('success-toast');
    document.getElementById('success-toast-msg').textContent = msg;
    toast.style.display = 'flex';
    toast.style.opacity = '1';
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => { toast.style.display = 'none'; }, 400); }, 3500);
}

// ── Result handlers ────────────────────────────────────────────────────────
function onCaptureSuccess(template) {
    if (!template) {
        onCaptureError('No fingerprint data received from scanner. Please try again.');
        return;
    }
    capturedTemplate = template;
    setStatus('Fingerprint captured!', 'Saving to database…', 'success');
    saveFingerprint();
}

function onDuplicateDetected(memberName) {
    setStatus('Duplicate fingerprint', 'Use a different finger and try again', 'error');
    const el = document.getElementById('error-msg');
    el.textContent = `This fingerprint is already enrolled for ${memberName}. Each person must use their own unique finger.`;
    el.classList.remove('hidden');
    document.getElementById('btn-retry').style.display = '';
    document.getElementById('btn-retry').textContent = 'Try a Different Finger';
    document.getElementById('btn-retry').onclick = retryCapture;
}

function onCaptureError(msg) {
    setStatus('Capture failed', 'Click Retry to try again', 'error');
    const el = document.getElementById('error-msg');
    el.textContent = msg;
    el.classList.remove('hidden');
    document.getElementById('btn-retry').style.display = '';
    document.getElementById('btn-retry').textContent = 'Retry Capture';
    document.getElementById('btn-retry').onclick = retryCapture;
}

// ── Save to server ─────────────────────────────────────────────────────────
async function saveFingerprint() {
    if (!capturedTemplate || saving) return;
    saving = true;

    const fingerIndex = document.getElementById('finger-select').value;
    const fingerLabel = document.getElementById('finger-select').selectedOptions[0].text;
    document.getElementById('saving-indicator').classList.remove('hidden');

    try {
        const res = await fetch(ENROLL_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify({ fingerprint_template: capturedTemplate, finger_index: fingerIndex }),
        });

        const rawText = await res.text();
        console.log('[Biometric] Server response', res.status, rawText);
        let json;
        try { json = JSON.parse(rawText); } catch(_) { json = {}; }
        document.getElementById('saving-indicator').classList.add('hidden');

        if (json.success) {
            const label = fingerLabel.replace(' (recommended)', '').replace(' (optional)', '');
            showToast(label + ' enrolled successfully!');
            setTimeout(() => window.location.reload(), 2500);
        } else {
            saving = false;
            onCaptureError(json.message || 'Save failed. Please try again.');
        }
    } catch (err) {
        document.getElementById('saving-indicator').classList.add('hidden');
        saving = false;
        onCaptureError('Network error. Please try again.');
    }
}

// ── Boot ───────────────────────────────────────────────────────────────────
init();
</script>
@endpush
@endsection
