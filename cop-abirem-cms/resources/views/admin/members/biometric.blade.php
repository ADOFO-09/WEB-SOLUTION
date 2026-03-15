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
        <p class="text-sm text-gray-500 mb-5">Connect your fingerprint scanner, then click Capture. The member should rest their finger on the scanner before clicking.</p>

        {{-- Fingerprint visual area --}}
        <div id="scanner-visual" class="flex flex-col items-center justify-center py-10 px-6 rounded-lg border-2 border-dashed border-gray-300 mb-5 transition-colors duration-300">
            <div id="fp-icon" class="mb-4">
                <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                </svg>
            </div>
            <p id="scanner-status" class="text-base font-medium text-gray-500">Ready to scan</p>
            <p id="scanner-sub" class="text-sm text-gray-400 mt-1">Select a finger and click Capture</p>
        </div>

        {{-- Finger selector & capture controls --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-4">
            <select id="finger-select" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="1">Primary Finger (recommended)</option>
                <option value="2">Backup Finger (optional)</option>
            </select>
            <button id="btn-capture" onclick="captureFingerprint()"
                class="inline-flex items-center justify-center gap-2 px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                </svg>
                Capture Fingerprint
            </button>
        </div>

        {{-- Test mode notice --}}
        <div id="test-mode-notice" class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700 mb-4">
            <strong>No scanner detected.</strong> Running in simulation mode — a test template will be used.
            Connect a supported fingerprint scanner (DigitalPersona, SecuGen, ZKTeco) and refresh the page.
        </div>

        {{-- Save button (hidden until capture succeeds) --}}
        <div id="save-section" class="hidden">
            <div class="p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 mb-3" id="capture-success-msg"></div>
            <button id="btn-save" onclick="saveFingerprint()"
                class="w-full py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                Save Fingerprint
            </button>
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
<script>
const ENROLL_URL  = "{{ route('admin.members.biometric.enroll', $member) }}";
const CSRF_TOKEN  = "{{ csrf_token() }}";

let capturedTemplate = null;

// ── Scanner detection & capture ────────────────────────────────────────────
// This layer attempts to use a locally-installed WebSocket fingerprint bridge
// (common approach for hardware scanners). Falls back to simulation mode.

const ws = (() => {
    try {
        const socket = new WebSocket('ws://localhost:15896/fingerprint');
        socket.onopen  = () => { setMode('connected'); };
        socket.onerror = () => { /* stays in simulation mode */ };
        socket.onmessage = (e) => {
            const data = JSON.parse(e.data);
            if (data.type === 'capture_result') {
                if (data.success) {
                    onCaptureSuccess(data.template);
                } else {
                    onCaptureError(data.message || 'Capture failed. Please try again.');
                }
            }
        };
        return socket;
    } catch (e) {
        return null;
    }
})();

function setMode(mode) {
    if (mode === 'connected') {
        document.getElementById('test-mode-notice').style.display = 'none';
    }
}

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
    visual.classList.add('flex','flex-col','items-center','justify-center','py-10','px-6','rounded-lg','border-2','border-dashed','transition-colors','duration-300', colors[color] || colors.idle);
}

async function captureFingerprint() {
    document.getElementById('btn-capture').disabled = true;
    document.getElementById('save-section').classList.add('hidden');
    document.getElementById('error-msg').classList.add('hidden');
    capturedTemplate = null;
    setStatus('Scanning…', 'Keep your finger on the scanner', 'scan');

    // Real scanner via WebSocket bridge
    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({ action: 'capture' }));
        return; // result comes via onmessage
    }

    // Simulation mode: generate a deterministic pseudo-template
    await new Promise(r => setTimeout(r, 1800));
    const simTemplate = btoa('SIM_FP_' + Date.now() + '_' + Math.random().toString(36).slice(2));
    onCaptureSuccess(simTemplate);
}

function onCaptureSuccess(template) {
    capturedTemplate = template;
    const fingerLabel = document.getElementById('finger-select').selectedOptions[0].text;
    setStatus('Fingerprint captured!', 'Ready to save', 'success');
    document.getElementById('capture-success-msg').textContent =
        fingerLabel + ' captured successfully. Click Save to store it.';
    document.getElementById('save-section').classList.remove('hidden');
    document.getElementById('btn-capture').disabled = false;
}

function onCaptureError(msg) {
    setStatus('Capture failed', 'Please try again', 'error');
    const el = document.getElementById('error-msg');
    el.textContent = msg;
    el.classList.remove('hidden');
    document.getElementById('btn-capture').disabled = false;
}

// ── Save to server ─────────────────────────────────────────────────────────
async function saveFingerprint() {
    if (!capturedTemplate) return;

    const fingerIndex = document.getElementById('finger-select').value;
    document.getElementById('btn-save').disabled = true;
    document.getElementById('btn-save').textContent = 'Saving…';

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

        const json = await res.json();

        if (json.success) {
            // Reload page to update status cards
            window.location.reload();
        } else {
            onCaptureError(json.message || 'Save failed.');
            document.getElementById('btn-save').disabled = false;
            document.getElementById('btn-save').textContent = 'Save Fingerprint';
        }
    } catch (err) {
        onCaptureError('Network error. Please try again.');
        document.getElementById('btn-save').disabled = false;
        document.getElementById('btn-save').textContent = 'Save Fingerprint';
    }
}
</script>
@endpush
@endsection
