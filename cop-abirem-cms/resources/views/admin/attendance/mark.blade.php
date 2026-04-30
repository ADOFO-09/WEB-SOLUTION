@extends('layouts.admin')

@section('title', 'Mark Attendance')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.attendance.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mark Attendance</h1>
                <p class="text-sm text-gray-500">{{ $attendance->serviceType->name ?? 'Service' }} - {{ $attendance->formatted_date }}</p>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
            <button onclick="openBiometricPanel()"
                class="inline-flex items-center px-4 py-2 border border-purple-500 rounded-md shadow-sm text-sm font-medium text-purple-600 bg-white hover:bg-purple-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                </svg>
                Biometric Scan
            </button>
            <a href="{{ route('admin.attendance.qr-display', $attendance) }}"
               class="inline-flex items-center px-4 py-2 border border-indigo-500 rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-white hover:bg-indigo-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                Display QR Code
            </a>
            <form action="{{ route('admin.attendance.close', $attendance) }}" method="POST"
                  onsubmit="return confirm('Close this session? You can reopen it later if needed.');">
                @csrf
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Close Session
                </button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<style>
@keyframes bio-pulse-ring {
    0%,100% { border-color: #a78bfa; }
    50%      { border-color: #6d28d9; opacity:.6; }
}
@keyframes bio-pulse-icon {
    0%,100% { transform: scale(1); }
    50%      { transform: scale(1.1); }
}
#bio-ring.bio-scanning {
    border-style: solid;
    animation: bio-pulse-ring 1.4s ease-in-out infinite;
}
#bio-ring.bio-success {
    border-style: solid; border-color: #34d399;
    background-color: rgba(52,211,153,.1);
}
#bio-ring.bio-error {
    border-style: solid; border-color: #f87171;
    background-color: rgba(248,113,113,.08);
}
#bio-fp-icon.bio-scanning { color:#a78bfa; animation: bio-pulse-icon 1.4s ease-in-out infinite; }
#bio-fp-icon.bio-success  { color:#34d399; }
#bio-fp-icon.bio-error    { color:#f87171; }
</style>
{{-- QR info banner --}}
<div class="mb-5 flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3">
    <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div class="flex-1 text-sm text-blue-700">
        Members can mark their own attendance by scanning the <strong>Session QR Code</strong> in the Member Portal.
        Use this page for <strong>manual attendance only</strong> (e.g. members without smartphones).
    </div>
    <a href="{{ route('admin.attendance.qr-display', $attendance) }}"
       class="flex-shrink-0 text-sm font-medium text-blue-600 hover:text-blue-800 whitespace-nowrap">
        View QR Code →
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left: Mark Attendance -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Quick Stats -->
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-3xl font-bold text-indigo-600" id="total-count">{{ $records->count() }}</div>
                <div class="text-sm text-gray-500">Total</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-3xl font-bold text-blue-600" id="members-count">{{ $records->whereNotNull('member_id')->count() }}</div>
                <div class="text-sm text-gray-500">Members</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-3xl font-bold text-green-600" id="visitors-count">{{ $records->whereNotNull('visitor_id')->count() }}</div>
                <div class="text-sm text-gray-500">Visitors</div>
            </div>
        </div>

        <!-- Member Search & Mark -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Mark Member Attendance</h3>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <input type="text" id="member-search" placeholder="Search member by name or ID..." 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div id="member-list" class="max-h-64 overflow-y-auto space-y-2">
                    @foreach($availableMembers as $member)
                    <div class="member-item flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100"
                         data-name="{{ strtolower($member->full_name) }}" data-id="{{ strtolower($member->member_id) }}">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($member->photo_path)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $member->photo_path) }}" alt="">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-600 font-medium text-sm">{{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $member->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $member->member_id }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="markMember({{ $member->id }}, this)" 
                                class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                            Mark
                        </button>
                    </div>
                    @endforeach
                    @if($availableMembers->isEmpty())
                    <p class="text-center text-gray-500 py-4">All members have been marked</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Visitor Mark -->
        @if($availableVisitors->count() > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Mark Visitor Attendance</h3>
            </div>
            <div class="p-6">
                <div class="space-y-2">
                    @foreach($availableVisitors as $visitor)
                    <div class="visitor-item flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $visitor->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $visitor->phone }}</p>
                        </div>
                        <button type="button" onclick="markVisitor({{ $visitor->id }}, this)" 
                                class="px-3 py-1 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                            Mark
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Right: Attendance List -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow sticky top-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Marked Present</h3>
            </div>
            <div id="attendance-list" class="max-h-[600px] overflow-y-auto divide-y divide-gray-200">
                @foreach($records as $record)
                <div class="attendance-record px-4 py-3 flex items-center justify-between" data-id="{{ $record->id }}">
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $record->member->full_name ?? $record->visitor->full_name ?? 'Unknown' }}
                            @if($record->visitor_id)
                            <span class="text-xs text-green-600">(Visitor)</span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $record->check_in_time->format('g:i A') }}
                            @if($record->is_late)
                            <span class="text-yellow-600">(Late)</span>
                            @endif
                        </p>
                    </div>
                    <button type="button" onclick="unmarkAttendance({{ $record->id }}, this)" 
                            class="text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                @endforeach
                @if($records->isEmpty())
                <p id="no-records" class="px-4 py-8 text-center text-gray-500">No one marked yet</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Biometric Scanner Panel --}}
<div id="bio-overlay" class="fixed inset-0 z-50 hidden" aria-modal="true">
    <div class="absolute inset-0 bg-black bg-opacity-60" onclick="closeBiometricPanel()"></div>
    <div id="bio-panel" class="absolute right-0 top-0 h-full w-full max-w-sm bg-gray-900 text-white shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300">
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-700">
            <div class="flex items-center gap-3">
                <div id="bio-status-dot" class="w-3 h-3 rounded-full bg-gray-500 flex-shrink-0"></div>
                <div>
                    <h2 class="text-sm font-semibold text-white">Biometric Scanner</h2>
                    <p id="bio-status-text" class="text-xs text-gray-400">Initialising…</p>
                </div>
            </div>
            <button onclick="closeBiometricPanel()" class="text-gray-400 hover:text-white ml-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Fingerprint ring --}}
        <div class="flex flex-col items-center justify-center py-8 px-6">
            <div id="bio-ring" class="relative flex items-center justify-center w-36 h-36 rounded-full border-4 border-dashed border-gray-600 transition-all duration-500">
                <svg id="bio-fp-icon" class="w-16 h-16 text-gray-500 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                </svg>
            </div>
            <p id="bio-instruction" class="mt-4 text-sm text-gray-300 text-center">Place finger on scanner</p>
            <p id="bio-match-name" class="mt-1 text-base font-semibold text-green-400 hidden"></p>
        </div>

        {{-- Stats --}}
        <div class="px-5 pb-4 flex justify-around text-center border-b border-gray-700">
            <div>
                <p class="text-2xl font-bold text-purple-400" id="bio-scan-count">0</p>
                <p class="text-xs text-gray-400">Scanned today</p>
            </div>
            <div>
                <p class="text-lg font-semibold text-gray-300" id="bio-enrolled-count">—</p>
                <p class="text-xs text-gray-400">Enrolled members</p>
            </div>
        </div>

        {{-- Recent check-ins --}}
        <div class="flex-1 overflow-y-auto">
            <p class="px-5 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Recent check-ins</p>
            <div id="bio-recent-list">
                <p id="bio-no-scans" class="px-5 py-6 text-sm text-gray-500 text-center">No scans yet</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-5 py-3 border-t border-gray-700 text-center">
            <a href="{{ route('admin.attendance.biometric', $attendance) }}" target="_blank"
               class="text-xs text-purple-400 hover:text-purple-300">Open full-screen kiosk ↗</a>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-4 right-4 transform translate-y-full opacity-0 transition-all duration-300">
    <div class="bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg">
        <span id="toast-message"></span>
    </div>
</div>

@push('scripts')
<script>
const sessionId = {{ $attendance->id }};

// Member search filter
document.getElementById('member-search').addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    document.querySelectorAll('.member-item').forEach(item => {
        const name = item.dataset.name;
        const id = item.dataset.id;
        if (name.includes(search) || id.includes(search)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
});

function showToast(message, isError = false) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    toastMessage.textContent = message;
    toast.firstElementChild.className = `${isError ? 'bg-red-600' : 'bg-gray-800'} text-white px-6 py-3 rounded-lg shadow-lg`;
    toast.classList.remove('translate-y-full', 'opacity-0');
    setTimeout(() => {
        toast.classList.add('translate-y-full', 'opacity-0');
    }, 3000);
}

function updateCounts(totals) {
    document.getElementById('total-count').textContent = totals.total;
    document.getElementById('members-count').textContent = totals.members;
    document.getElementById('visitors-count').textContent = totals.visitors;
}

function markMember(memberId, button) {
    button.disabled = true;
    button.textContent = 'Marking...';
    
    fetch(`/admin/attendance/${sessionId}/mark-member`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ member_id: memberId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message);
            button.closest('.member-item').remove();
            addToAttendanceList(data.record);
            updateCounts(data.totals);
            document.getElementById('no-records')?.remove();
        } else {
            showToast(data.message, true);
            button.disabled = false;
            button.textContent = 'Mark';
        }
    })
    .catch(() => {
        showToast('Error marking attendance', true);
        button.disabled = false;
        button.textContent = 'Mark';
    });
}

function markVisitor(visitorId, button) {
    button.disabled = true;
    button.textContent = 'Marking...';
    
    fetch(`/admin/attendance/${sessionId}/mark-visitor`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ visitor_id: visitorId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message);
            button.closest('.visitor-item').remove();
            addToAttendanceList(data.record);
            updateCounts(data.totals);
            document.getElementById('no-records')?.remove();
        } else {
            showToast(data.message, true);
            button.disabled = false;
            button.textContent = 'Mark';
        }
    })
    .catch(() => {
        showToast('Error marking attendance', true);
        button.disabled = false;
        button.textContent = 'Mark';
    });
}

function addToAttendanceList(record) {
    const list = document.getElementById('attendance-list');
    const html = `
        <div class="attendance-record px-4 py-3 flex items-center justify-between" data-id="${record.id}">
            <div>
                <p class="text-sm font-medium text-gray-900">
                    ${record.name}
                    ${record.type === 'visitor' ? '<span class="text-xs text-green-600">(Visitor)</span>' : ''}
                </p>
                <p class="text-xs text-gray-500">
                    ${record.check_in_time}
                    ${record.is_late ? '<span class="text-yellow-600">(Late)</span>' : ''}
                </p>
            </div>
            <button type="button" onclick="unmarkAttendance(${record.id}, this)" class="text-red-500 hover:text-red-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    list.insertAdjacentHTML('afterbegin', html);
}

function unmarkAttendance(recordId, button) {
    if (!confirm('Remove this attendance record?')) return;
    
    fetch(`/admin/attendance/${sessionId}/unmark`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ record_id: recordId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message);
            button.closest('.attendance-record').remove();
            updateCounts(data.totals);
        } else {
            showToast(data.message, true);
        }
    })
    .catch(() => {
        showToast('Error removing attendance', true);
    });
}
</script>

{{-- ===== Biometric Scanner Panel JS ===== --}}
<script>
const MEMBERS_URL = '{{ route('admin.attendance.biometric.members', $attendance) }}';
const VERIFY_URL  = '{{ route('admin.attendance.biometric.verify') }}';
const BIO_CSRF    = '{{ csrf_token() }}';

let bioWs          = null;
let bioEnrolled    = [];
let bioCheckedInIds = new Set();
let bioScanCount   = 0;
let bioPanelOpen   = false;

function openBiometricPanel() {
    const overlay = document.getElementById('bio-overlay');
    const panel   = document.getElementById('bio-panel');
    overlay.classList.remove('hidden');
    requestAnimationFrame(() => panel.classList.remove('translate-x-full'));
    bioPanelOpen = true;
    if (!bioWs) initBiometric();
}

function closeBiometricPanel() {
    const overlay = document.getElementById('bio-overlay');
    const panel   = document.getElementById('bio-panel');
    panel.classList.add('translate-x-full');
    setTimeout(() => overlay.classList.add('hidden'), 300);
    bioPanelOpen = false;
    if (bioWs) { bioWs.close(); bioWs = null; }
}

async function initBiometric() {
    setBioStatus('loading', 'Loading enrolled members…');
    try {
        const res  = await fetch(MEMBERS_URL);
        const data = await res.json();
        bioEnrolled    = data.members || [];
        bioCheckedInIds = new Set((data.checked_in_ids || []).map(Number));
        document.getElementById('bio-enrolled-count').textContent = bioEnrolled.length;
        connectBioWs();
    } catch (e) {
        setBioStatus('error', 'Failed to load members');
    }
}

function connectBioWs() {
    if (bioWs) return;
    setBioStatus('loading', 'Connecting to scanner…');
    try {
        bioWs = new WebSocket('ws://localhost:15896/fingerprint');
    } catch (e) {
        setBioStatus('error', 'Cannot reach scanner bridge');
        return;
    }

    bioWs.onopen = () => {
        setBioStatus('scanning', 'Ready — place finger on scanner');
        setRingState('scanning');
        const templates = bioEnrolled.flatMap(m => {
            const ts = [];
            if (m.t1) ts.push({ member_id: m.id, template: m.t1 });
            if (m.t2) ts.push({ member_id: m.id, template: m.t2 });
            return ts;
        });
        bioWs.send(JSON.stringify({ action: 'start_identify', templates }));
    };

    bioWs.onmessage = evt => {
        let msg;
        try { msg = JSON.parse(evt.data); } catch { return; }
        if (msg.type === 'identify_result') handleBioIdentify(msg);
    };

    bioWs.onerror = () => setBioStatus('error', 'Scanner bridge error');

    bioWs.onclose = () => {
        bioWs = null;
        if (bioPanelOpen) setBioStatus('error', 'Disconnected — close and reopen to retry');
        setRingState('idle');
    };
}

function handleBioIdentify(msg) {
    if (!msg.matched) {
        setRingState('error');
        setBioStatus('error', 'No match found');
        document.getElementById('bio-instruction').textContent = 'Fingerprint not recognised. Try again.';
        setTimeout(() => resetBioRing(), 2000);
        return;
    }

    const memberId = Number(msg.member_id);

    if (bioCheckedInIds.has(memberId)) {
        const m = bioEnrolled.find(x => x.id === memberId);
        setRingState('error');
        setBioStatus('error', (m ? m.name : 'Member') + ' already checked in');
        setTimeout(() => resetBioRing(), 2500);
        return;
    }

    const m = bioEnrolled.find(x => x.id === memberId);
    setRingState('success');
    setBioStatus('success', 'Match found!');
    document.getElementById('bio-instruction').textContent = 'Match found!';
    if (m) {
        const nm = document.getElementById('bio-match-name');
        nm.textContent = m.name;
        nm.classList.remove('hidden');
    }

    fetch(VERIFY_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': BIO_CSRF },
        body: JSON.stringify({ session_id: sessionId, member_id: memberId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bioCheckedInIds.add(memberId);
            bioScanCount++;
            document.getElementById('bio-scan-count').textContent = bioScanCount;

            // Remove from available members list
            const btn = document.querySelector(`#member-list [onclick*="markMember(${memberId},"]`);
            btn?.closest('.member-item')?.remove();

            // Add to Marked Present sidebar
            addToAttendanceList({
                id: data.record_id,
                name: data.member_name,
                type: 'member',
                check_in_time: data.check_in_time,
                is_late: false
            });
            document.getElementById('no-records')?.remove();

            // Increment counters manually (biometric is always a member)
            const tc = document.getElementById('total-count');
            const mc = document.getElementById('members-count');
            tc.textContent = parseInt(tc.textContent) + 1;
            mc.textContent = parseInt(mc.textContent) + 1;

            addBioRecentEntry(data.member_name, data.check_in_time);
            showToast('✓ ' + data.member_name + ' checked in via fingerprint');
        } else if (data.already_in) {
            bioCheckedInIds.add(memberId);
            showToast(data.message, true);
        } else {
            showToast(data.message || 'Error recording attendance', true);
        }
    })
    .catch(() => showToast('Network error recording attendance', true))
    .finally(() => setTimeout(() => {
        resetBioRing();
        document.getElementById('bio-match-name').classList.add('hidden');
    }, 2500));
}

function resetBioRing() {
    setRingState('scanning');
    setBioStatus('scanning', 'Ready — place next finger');
    document.getElementById('bio-instruction').textContent = 'Place finger on scanner';
}

function addBioRecentEntry(name, time) {
    document.getElementById('bio-no-scans')?.remove();
    document.getElementById('bio-recent-list').insertAdjacentHTML('afterbegin', `
        <div class="flex items-center gap-3 px-5 py-3 border-b border-gray-700/50">
            <div class="w-7 h-7 rounded-full bg-green-800 flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">${name}</p>
                <p class="text-xs text-gray-400">${time}</p>
            </div>
        </div>`);
}

function setBioStatus(state, text) {
    document.getElementById('bio-status-text').textContent = text;
    const colours = {
        loading:  'bg-yellow-400 animate-pulse',
        idle:     'bg-gray-400',
        scanning: 'bg-purple-400 animate-pulse',
        success:  'bg-green-400',
        error:    'bg-red-400',
    };
    document.getElementById('bio-status-dot').className =
        'w-3 h-3 rounded-full flex-shrink-0 transition-colors duration-300 ' + (colours[state] || colours.idle);
}

function setRingState(state) {
    const ring = document.getElementById('bio-ring');
    const icon = document.getElementById('bio-fp-icon');
    ring.className = 'relative flex items-center justify-center w-36 h-36 rounded-full border-4 border-dashed border-gray-600 transition-all duration-500';
    icon.className = 'w-16 h-16 text-gray-500 transition-all duration-300';
    if (state === 'scanning') {
        ring.classList.add('bio-scanning');
        icon.classList.add('bio-scanning');
        icon.classList.remove('text-gray-500');
    } else if (state === 'success') {
        ring.classList.add('bio-success');
        icon.classList.add('bio-success');
        icon.classList.remove('text-gray-500');
    } else if (state === 'error') {
        ring.classList.add('bio-error');
        icon.classList.add('bio-error');
        icon.classList.remove('text-gray-500');
    }
}
</script>
@endpush
@endsection
