<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Biometric Attendance — {{ $session->serviceType->name ?? 'Service' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #f8fafc;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            user-select: none;
        }

        /* ── Header ─────────────────────────────── */
        .header {
            text-align: center;
            padding: 1.5rem 2rem 1rem;
            background: #1e293b;
            border-bottom: 1px solid #334155;
            flex-shrink: 0;
        }
        .header-church { font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.15em; color: #94a3b8; }
        .header-title  { font-size: 1.5rem; font-weight: 700; color: #f1f5f9; margin: 0.25rem 0; }
        .header-sub    { font-size: 0.875rem; color: #64748b; }

        /* ── Main scanner area ─────────────────── */
        .scanner-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            gap: 1.5rem;
        }

        .fp-ring {
            width: 180px; height: 180px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 4px solid #334155;
            transition: all 0.4s ease;
            position: relative;
        }
        .fp-ring.scanning {
            border-color: #6366f1;
            animation: pulse-ring 1.2s ease-in-out infinite;
        }
        .fp-ring.success {
            border-color: #10b981;
            animation: none;
            background: rgba(16,185,129,0.1);
        }
        .fp-ring.error {
            border-color: #ef4444;
            animation: shake 0.4s ease;
        }
        @keyframes pulse-ring {
            0%, 100% { box-shadow: 0 0 0 0 rgba(99,102,241,0.4); }
            50%       { box-shadow: 0 0 0 20px rgba(99,102,241,0); }
        }
        @keyframes shake {
            0%,100% { transform: translateX(0); }
            25%      { transform: translateX(-8px); }
            75%      { transform: translateX(8px); }
        }

        .fp-icon { color: #475569; transition: color 0.3s; }
        .fp-ring.scanning .fp-icon { color: #6366f1; }
        .fp-ring.success  .fp-icon { color: #10b981; }
        .fp-ring.error    .fp-icon { color: #ef4444; }

        .scanner-prompt {
            font-size: 1.25rem;
            font-weight: 600;
            color: #cbd5e1;
            text-align: center;
        }
        .scanner-sub {
            font-size: 0.875rem;
            color: #64748b;
            text-align: center;
            min-height: 1.25em;
        }

        /* ── Result flash ──────────────────────── */
        .result-flash {
            display: none;
            align-items: center;
            gap: 1rem;
            background: #1e293b;
            border: 2px solid #334155;
            border-radius: 1rem;
            padding: 1rem 1.5rem;
            width: 100%;
            max-width: 420px;
            transition: border-color 0.3s;
        }
        .result-flash.show { display: flex; }
        .result-flash.success { border-color: #10b981; }
        .result-flash.error   { border-color: #ef4444; }
        .result-flash.warning { border-color: #f59e0b; }

        .result-avatar {
            width: 3rem; height: 3rem;
            border-radius: 50%;
            background: #334155;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1rem; color: #94a3b8;
            flex-shrink: 0;
            overflow: hidden;
        }
        .result-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .result-name  { font-size: 1rem; font-weight: 600; color: #f1f5f9; }
        .result-meta  { font-size: 0.75rem; color: #64748b; margin-top: 0.125rem; }

        /* ── Recent scans log ──────────────────── */
        .log-area {
            width: 100%; max-width: 480px;
            max-height: 9rem;
            overflow-y: auto;
        }
        .log-entry {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #1e293b;
            animation: slide-in 0.3s ease;
        }
        @keyframes slide-in {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .log-dot {
            width: 0.5rem; height: 0.5rem;
            border-radius: 50%; background: #10b981; flex-shrink: 0;
        }
        .log-name  { flex: 1; font-size: 0.8125rem; color: #94a3b8; }
        .log-time  { font-size: 0.75rem; color: #475569; }

        /* ── Footer counter ────────────────────── */
        .footer {
            text-align: center;
            padding: 1rem 2rem;
            background: #1e293b;
            border-top: 1px solid #334155;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .attendance-count {
            font-size: 1.125rem;
            font-weight: 700;
            color: #f1f5f9;
        }
        .attendance-label { font-size: 0.75rem; color: #64748b; }
        .exit-btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: transparent;
            border: 1px solid #334155;
            border-radius: 0.5rem;
            color: #64748b;
            font-size: 0.8125rem;
            cursor: pointer;
            text-decoration: none;
            transition: border-color 0.2s, color 0.2s;
        }
        .exit-btn:hover { border-color: #64748b; color: #94a3b8; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div class="header-church">COP Abirem Church</div>
        <div class="header-title">Biometric Attendance</div>
        <div class="header-sub">
            {{ $session->serviceType->name ?? 'Service' }}
            &nbsp;·&nbsp;
            {{ $session->service_date->format('l, F j, Y') }}
        </div>
    </div>

    {{-- Scanner Area --}}
    <div class="scanner-area">

        {{-- Fingerprint ring --}}
        <div class="fp-ring" id="fp-ring">
            <svg class="fp-icon" width="80" height="80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
                    d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
            </svg>
        </div>

        <div>
            <p class="scanner-prompt" id="scanner-prompt">Place your finger on the scanner</p>
            <p class="scanner-sub" id="scanner-sub">&nbsp;</p>
        </div>

        {{-- Result flash --}}
        <div class="result-flash" id="result-flash">
            <div class="result-avatar" id="result-avatar"></div>
            <div>
                <div class="result-name" id="result-name"></div>
                <div class="result-meta" id="result-meta"></div>
            </div>
        </div>

        {{-- Recent scans log --}}
        <div class="log-area" id="log-area"></div>

    </div>

    {{-- Footer --}}
    <div class="footer">
        <div>
            <div class="attendance-count">
                <span id="total-count">{{ $scanCount }}</span> checked in via biometric
            </div>
            <div class="attendance-label">Session total shown on the main attendance page</div>
        </div>
        <a href="{{ route('admin.attendance.show', $session) }}" class="exit-btn">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Exit Station
        </a>
    </div>

<script>
const SESSION_ID     = {{ $session->id }};
const VERIFY_URL     = "{{ route('admin.attendance.biometric.verify') }}";
const CSRF_TOKEN     = document.querySelector('meta[name="csrf-token"]').content;

// Members sent from server for client-side matching
const ENROLLED_MEMBERS = @json($enrolledMembersJson);

// IDs already checked in — skip them
const CHECKED_IN = new Set(@json($checkedInIds));

// ── WebSocket scanner bridge ───────────────────────────────────────────────
// Connects to a locally-running fingerprint bridge server.
// Protocol: send { action: "capture" } → receive { type: "capture_result", success: bool, template: string }
let ws = null;
let scanning = false;

function connectScanner() {
    try {
        ws = new WebSocket('ws://localhost:15896/fingerprint');
        ws.onopen    = () => setPrompt('Place your finger on the scanner', '');
        ws.onclose   = () => { ws = null; startSimulationMode(); };
        ws.onerror   = () => { ws = null; startSimulationMode(); };
        ws.onmessage = (e) => {
            const d = JSON.parse(e.data);
            if (d.type === 'capture_result') {
                if (d.success) processTemplate(d.template);
                else           onScanError('Scan failed. Try again.');
            }
        };
    } catch (_) { startSimulationMode(); }
}

// Simulation mode: auto-triggers every 6s using a random enrolled member template
// In production with real hardware this loop is replaced by scanner events.
function startSimulationMode() {
    setPrompt('Place your finger on the scanner', '(Simulation mode — no scanner detected)');
    if (ENROLLED_MEMBERS.length === 0) return;
    triggerSimScan();
}

function triggerSimScan() {
    if (scanning) return;
    setTimeout(() => {
        const m = ENROLLED_MEMBERS[Math.floor(Math.random() * ENROLLED_MEMBERS.length)];
        if (m) processTemplate(m.t1, true);
    }, 4000);
}

// ── Fingerprint matching ───────────────────────────────────────────────────
// Simple exact-match comparison (templates are stored verbatim from the scanner).
// In production use the scanner SDK's compare() method for fuzzy matching.
function matchTemplate(template) {
    for (const m of ENROLLED_MEMBERS) {
        if (m.t1 === template || (m.t2 && m.t2 === template)) {
            return m;
        }
    }
    return null;
}

async function processTemplate(template, isSim = false) {
    if (scanning) return;
    scanning = true;
    setRingState('scanning');
    setPrompt('Matching…', '');

    await sleep(isSim ? 800 : 300);

    const matched = matchTemplate(template);

    if (!matched) {
        onScanError('Fingerprint not recognised. Please try again.');
        scanning = false;
        return;
    }

    if (CHECKED_IN.has(matched.id)) {
        showResult('warning', matched, 'Already checked in today');
        scanning = false;
        resetAfter(3000);
        return;
    }

    // Send to server
    try {
        const res = await fetch(VERIFY_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, Accept: 'application/json' },
            body: JSON.stringify({ session_id: SESSION_ID, member_id: matched.id }),
        });
        const json = await res.json();

        if (json.success) {
            CHECKED_IN.add(matched.id);
            document.getElementById('total-count').textContent = json.total_count;
            showResult('success', matched, 'Checked in · ' + json.check_in_time);
            addLog(matched.name, json.check_in_time);
            setRingState('success');
        } else if (json.already_in) {
            showResult('warning', matched, 'Already checked in today');
            CHECKED_IN.add(matched.id);
        } else {
            onScanError(json.message || 'Could not record attendance.');
        }
    } catch (_) {
        onScanError('Network error. Please check the connection.');
    }

    scanning = false;
    resetAfter(3000);
}

// ── UI helpers ─────────────────────────────────────────────────────────────
function setRingState(state) {
    const ring = document.getElementById('fp-ring');
    ring.className = 'fp-ring ' + state;
}

function setPrompt(text, sub) {
    document.getElementById('scanner-prompt').textContent = text;
    document.getElementById('scanner-sub').textContent = sub || '\u00a0';
}

function showResult(type, member, meta) {
    const flash  = document.getElementById('result-flash');
    const avatar = document.getElementById('result-avatar');
    flash.className = 'result-flash show ' + type;
    document.getElementById('result-name').textContent = member.name;
    document.getElementById('result-meta').textContent = meta;
    avatar.innerHTML = member.photo
        ? `<img src="${member.photo}" alt="">`
        : member.name.split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase();
}

function addLog(name, time) {
    const log  = document.getElementById('log-area');
    const entry = document.createElement('div');
    entry.className = 'log-entry';
    entry.innerHTML = `<div class="log-dot"></div><div class="log-name">${name}</div><div class="log-time">${time}</div>`;
    log.prepend(entry);
}

function onScanError(msg) {
    setRingState('error');
    setPrompt('Not recognised', msg);
    const flash = document.getElementById('result-flash');
    flash.className = 'result-flash show error';
    document.getElementById('result-name').textContent = 'Fingerprint not found';
    document.getElementById('result-meta').textContent = msg;
    document.getElementById('result-avatar').innerHTML = '?';
    resetAfter(3000);
}

function resetAfter(ms) {
    setTimeout(() => {
        setRingState('');
        setPrompt('Place your finger on the scanner', ws ? '' : '(Simulation mode — no scanner detected)');
        document.getElementById('result-flash').className = 'result-flash';
        if (!ws) triggerSimScan();
    }, ms);
}

function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

// ── Boot ───────────────────────────────────────────────────────────────────
connectScanner();

// Prevent accidental page navigation
window.onbeforeunload = (e) => {
    if (document.activeElement?.tagName === 'A') return; // allow exit button
    e.preventDefault();
};
</script>
</body>
</html>
