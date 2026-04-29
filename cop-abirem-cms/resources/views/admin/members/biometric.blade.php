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

        {{-- Bridge connection notice --}}
        <div id="test-mode-notice" class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700 mb-4">
            <strong>Connecting to scanner bridge…</strong> Make sure <code>run-bridge.bat</code> is running as Administrator.
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
const ENROLL_URL = "{{ route('admin.members.biometric.enroll', $member) }}";
const CSRF_TOKEN = "{{ csrf_token() }}";

let capturedTemplate = null;
let ws = null;
let bridgeConnected = false;
let saving = false;

// ── WebSocket bridge ───────────────────────────────────────────────────────
function connectBridge() {
    try {
        ws = new WebSocket('ws://localhost:15896/fingerprint');

        ws.onopen = () => {
            bridgeConnected = true;
            document.getElementById('test-mode-notice').style.display = 'none';
            document.getElementById('btn-retry').style.display = 'none';
            // Auto-start capture immediately — user just needs to place finger
            startCapture();
        };

        ws.onclose = () => {
            bridgeConnected = false;
            ws = null;
            if (!saving) startSimulationMode();
        };

        ws.onerror = () => {
            bridgeConnected = false;
            ws = null;
            startSimulationMode();
        };

        ws.onmessage = (e) => {
            let data;
            try { data = JSON.parse(e.data); } catch (_) { return; }
            if (data.type === 'capture_result') {
                if (data.success) {
                    onCaptureSuccess(data.template);
                } else {
                    onCaptureError(data.message || 'Capture failed. Try again.');
                }
            }
        };
    } catch (_) {
        startSimulationMode();
    }
}

// ── Start a capture request ────────────────────────────────────────────────
function startCapture() {
    if (saving) return;
    capturedTemplate = null;
    document.getElementById('saving-indicator').classList.add('hidden');
    document.getElementById('error-msg').classList.add('hidden');
    document.getElementById('btn-retry').style.display = 'none';

    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({ action: 'capture' }));
        setStatus('Place your finger on the scanner', 'Hold still until captured', 'scan');
    }
}

// ── Simulation fallback ────────────────────────────────────────────────────
function startSimulationMode() {
    document.getElementById('test-mode-notice').innerHTML =
        '<strong>No scanner bridge detected.</strong> Running in simulation mode — a test template will be used. ' +
        'Start <code>run-bridge.bat</code> as Administrator and refresh the page to use the real scanner.';
    document.getElementById('test-mode-notice').style.display = '';
    setStatus('Simulation mode', 'Click Retry to simulate a capture', 'idle');
    document.getElementById('btn-retry').style.display = '';
    document.getElementById('btn-retry').textContent = 'Simulate Capture';
    document.getElementById('btn-retry').onclick = runSimulation;
}

async function runSimulation() {
    document.getElementById('btn-retry').style.display = 'none';
    setStatus('Simulating scan…', 'Please wait', 'scan');
    await new Promise(r => setTimeout(r, 1500));
    const simTemplate = btoa('SIM_FP_' + Date.now() + '_' + Math.random().toString(36).slice(2));
    onCaptureSuccess(simTemplate);
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
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => { toast.style.display = 'none'; }, 400);
    }, 3500);
}

// ── Capture result handlers ────────────────────────────────────────────────
function onCaptureSuccess(template) {
    capturedTemplate = template;
    setStatus('Fingerprint captured!', 'Saving to database…', 'success');
    saveFingerprint();
}

function onCaptureError(msg) {
    setStatus('Capture failed', 'Click Retry to try again', 'error');
    const el = document.getElementById('error-msg');
    el.textContent = msg;
    el.classList.remove('hidden');
    // Show retry button — do NOT auto-retry, let admin read the error first
    document.getElementById('btn-retry').style.display = '';
    document.getElementById('btn-retry').textContent = 'Retry Capture';
    document.getElementById('btn-retry').onclick = startCapture;
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
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                fingerprint_template: capturedTemplate,
                finger_index: fingerIndex,
            }),
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
connectBridge();
</script>
@endpush
@endsection
