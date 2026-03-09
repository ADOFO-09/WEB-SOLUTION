@extends('layouts.member')

@section('title', 'Scan Attendance')

@section('header')
<h2 class="text-lg font-semibold text-gray-800">Scan Attendance</h2>
@endsection

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    {{-- Scanner Card --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Scan Session QR Code</h3>
        <p class="text-sm text-gray-500 mb-5">Point your camera at the QR code displayed at the church entrance.</p>

        {{-- Camera Scanner --}}
        <div id="reader" class="w-full rounded-lg overflow-hidden" style="min-height: 280px;"></div>

        <div id="scan-status" class="mt-3 text-center text-sm text-gray-400">Initialising camera…</div>

        {{-- Divider --}}
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
            <div class="relative flex justify-center">
                <span class="px-3 bg-white text-xs text-gray-400 uppercase tracking-wide">or enter code manually</span>
            </div>
        </div>

        {{-- Manual token entry --}}
        <form action="{{ route('member.attendance.verify', ['token' => '__TOKEN__']) }}" method="GET" id="manual-form">
            <div class="flex space-x-2">
                <input type="text" id="manual-token" name="_manual"
                       placeholder="Paste the attendance token here"
                       class="flex-1 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                    Verify
                </button>
            </div>
        </form>
    </div>

    {{-- Recent Attendance --}}
    @if($recentAttendance->count() > 0)
    <div class="bg-white rounded-xl shadow">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Recent Attendance</h3>
        </div>
        <ul class="divide-y divide-gray-100">
            @foreach($recentAttendance as $record)
            <li class="px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">
                        {{ $record->session->serviceType->name ?? 'Service' }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ $record->session->service_date->format('M d, Y') }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-500">{{ $record->check_in_time->format('g:i A') }}</span>
                    @if($record->is_late)
                    <span class="ml-1 px-1.5 py-0.5 text-xs bg-yellow-100 text-yellow-700 rounded-full">Late</span>
                    @else
                    <span class="ml-1 px-1.5 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">On time</span>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
        <div class="px-5 py-3 border-t border-gray-100">
            <a href="{{ route('member.attendance.index') }}" class="text-xs text-blue-600 hover:underline">
                View full attendance history →
            </a>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.4/html5-qrcode.min.js"></script>
<script>
const verifyBase = @json(route('member.attendance.scan'));  // base URL to build verify URL
let scanner;
let scanned = false;

document.addEventListener('DOMContentLoaded', () => {
    startScanner();
    setupManualForm();
});

function startScanner() {
    Html5Qrcode.getCameras().then(cameras => {
        if (!cameras || cameras.length === 0) {
            document.getElementById('scan-status').textContent = 'No camera found. Use manual entry below.';
            return;
        }

        scanner = new Html5Qrcode('reader');
        const cameraId = cameras[cameras.length - 1].id; // prefer back camera

        scanner.start(cameraId, { fps: 10, qrbox: { width: 250, height: 250 } },
            onScanSuccess,
            () => { /* ignore frame errors */ }
        ).then(() => {
            document.getElementById('scan-status').textContent = 'Camera active — point at the QR code.';
        }).catch(() => {
            document.getElementById('scan-status').textContent = 'Camera permission denied. Use manual entry.';
        });
    }).catch(() => {
        document.getElementById('scan-status').textContent = 'Camera not available. Use manual entry.';
    });
}

function onScanSuccess(decodedText) {
    if (scanned) return;
    scanned = true;

    // Stop scanner
    if (scanner) { scanner.stop().catch(() => {}); }

    document.getElementById('scan-status').textContent = 'QR code detected — redirecting…';

    // The QR code contains the full verify URL — navigate to it directly
    window.location.href = decodedText;
}

function setupManualForm() {
    document.getElementById('manual-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const token = document.getElementById('manual-token').value.trim();
        if (!token) return;

        // Build verify URL: replace __TOKEN__ in action with actual token
        const base = '{{ rtrim(url("/member/attendance/verify"), "/") }}/';
        window.location.href = base + encodeURIComponent(token);
    });
}
</script>
@endpush
@endsection
