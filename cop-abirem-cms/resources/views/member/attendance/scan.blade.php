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
                        {{ $record->session->service_date->format($dateFormat) }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-500">{{ $record->check_in_time->format($timeFormat) }}</span>
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
<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
<script>
let scanner;
let scanned = false;

document.addEventListener('DOMContentLoaded', () => {
    startScanner();
});

async function startScanner() {
    const status = document.getElementById('scan-status');

    // Camera API requires a secure context (HTTPS or localhost)
    if (!window.isSecureContext || !navigator.mediaDevices) {
        status.innerHTML = 'Camera requires <strong>HTTPS</strong>. Contact the admin to mark your attendance.';
        return;
    }

    // Step 1: trigger the browser permission prompt and populate device labels
    let stream;
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: true });
    } catch (err) {
        const msg = String(err).toLowerCase();
        if (msg.includes('notallowed') || msg.includes('permission') || msg.includes('denied')) {
            status.innerHTML = 'Camera access was blocked. Click the <strong>camera icon</strong> in your browser\'s address bar to allow access, then reload.';
        } else {
            status.textContent = 'No camera found on this device. Contact the admin to mark your attendance.';
        }
        return;
    }
    // Release the test stream so html5-qrcode can open the camera cleanly
    stream.getTracks().forEach(t => t.stop());

    // Step 2: enumerate cameras (labels are now available after permission grant)
    let cameras = [];
    try {
        cameras = await Html5Qrcode.getCameras();
    } catch (_) {}

    if (!cameras || cameras.length === 0) {
        status.textContent = 'No camera found on this device. Contact the admin to mark your attendance.';
        return;
    }

    // Prefer rear camera on mobile; fall back to last in list
    const chosen = cameras.find(c => /back|rear|environment/i.test(c.label)) ?? cameras[cameras.length - 1];

    // Step 3: start the scanner
    scanner = new Html5Qrcode('reader');
    try {
        await scanner.start(chosen.id, { fps: 10, qrbox: { width: 250, height: 250 } }, onScanSuccess, () => {});
        status.textContent = 'Camera active — point at the QR code.';
    } catch (err) {
        status.textContent = 'Could not start camera. Contact the admin to mark your attendance.';
        console.error('QR scanner error:', err);
    }
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


</script>
@endpush
@endsection
