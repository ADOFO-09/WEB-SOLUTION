@extends('layouts.admin')

@section('title', 'QR Scanner - Attendance')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.attendance.mark', $attendance) }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">QR Code Scanner</h1>
                <p class="text-sm text-gray-500">{{ $attendance->serviceType->name ?? 'Service' }} - {{ $attendance->service_date->format('M d, Y') }}</p>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-indigo-600" id="total-count">0</div>
            <div class="text-sm text-gray-500">Total</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-blue-600" id="members-count">0</div>
            <div class="text-sm text-gray-500">Members</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-green-600" id="visitors-count">0</div>
            <div class="text-sm text-gray-500">Visitors</div>
        </div>
    </div>

    <!-- Scanner -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div id="reader" class="w-full" style="min-height: 300px;"></div>
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500">Position the member's QR code in front of the camera</p>
            </div>
        </div>

        <!-- Result Display -->
        <div id="scan-result" class="hidden border-t border-gray-200 p-6">
            <div id="result-success" class="hidden text-center">
                <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 id="result-name" class="mt-4 text-lg font-medium text-gray-900"></h3>
                <p id="result-time" class="text-sm text-gray-500"></p>
            </div>
            <div id="result-error" class="hidden text-center">
                <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <p id="error-message" class="mt-4 text-red-600"></p>
            </div>
            <div id="result-duplicate" class="hidden text-center">
                <div class="w-16 h-16 mx-auto bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <p id="duplicate-message" class="mt-4 text-yellow-600"></p>
            </div>
        </div>
    </div>

    <!-- Recent Scans -->
    <div class="mt-6 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Scans</h3>
        </div>
        <ul id="recent-scans" class="divide-y divide-gray-200 max-h-64 overflow-y-auto">
            <li class="px-6 py-4 text-center text-gray-500">No scans yet</li>
        </ul>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.4/html5-qrcode.min.js"></script>
<script>
const sessionId = {{ $attendance->id }};
let html5QrcodeScanner;
let isProcessing = false;
let lastScannedCode = '';
let lastScannedTime = 0;

document.addEventListener('DOMContentLoaded', function() {
    initScanner();
});

function initScanner() {
    html5QrcodeScanner = new Html5QrcodeScanner("reader", {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    });
    
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
}

function onScanSuccess(decodedText, decodedResult) {
    // Prevent duplicate scans within 3 seconds
    const now = Date.now();
    if (decodedText === lastScannedCode && (now - lastScannedTime) < 3000) {
        return;
    }
    
    if (isProcessing) return;
    
    lastScannedCode = decodedText;
    lastScannedTime = now;
    isProcessing = true;
    
    // Play beep sound
    playBeep();
    
    // Process the scan
    processScan(decodedText);
}

function onScanFailure(error) {
    // Ignore scan failures (no QR code in view)
}

function processScan(qrData) {
    hideAllResults();
    
    fetch(`/admin/attendance/${sessionId}/process-scan`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ qr_data: qrData })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('scan-result').classList.remove('hidden');
        
        if (data.success) {
            showSuccess(data.member.name, data.record.check_in_time, data.record.is_late);
            updateCounts(data.totals);
            addToRecentScans(data.member.name, data.record.check_in_time, true);
        } else if (data.already_marked) {
            showDuplicate(data.message);
        } else {
            showError(data.message);
        }
        
        // Reset after 2 seconds
        setTimeout(() => {
            isProcessing = false;
            hideAllResults();
        }, 2000);
    })
    .catch(error => {
        showError('Failed to process scan');
        setTimeout(() => {
            isProcessing = false;
            hideAllResults();
        }, 2000);
    });
}

function showSuccess(name, time, isLate) {
    document.getElementById('result-success').classList.remove('hidden');
    document.getElementById('result-name').textContent = name;
    document.getElementById('result-time').textContent = time + (isLate ? ' (Late)' : '');
}

function showError(message) {
    document.getElementById('result-error').classList.remove('hidden');
    document.getElementById('error-message').textContent = message;
}

function showDuplicate(message) {
    document.getElementById('result-duplicate').classList.remove('hidden');
    document.getElementById('duplicate-message').textContent = message;
}

function hideAllResults() {
    document.getElementById('scan-result').classList.add('hidden');
    document.getElementById('result-success').classList.add('hidden');
    document.getElementById('result-error').classList.add('hidden');
    document.getElementById('result-duplicate').classList.add('hidden');
}

function updateCounts(totals) {
    document.getElementById('total-count').textContent = totals.total;
    document.getElementById('members-count').textContent = totals.members;
    document.getElementById('visitors-count').textContent = totals.visitors;
}

function addToRecentScans(name, time, success) {
    const list = document.getElementById('recent-scans');
    
    // Remove "no scans" message if present
    const noScans = list.querySelector('.text-gray-500');
    if (noScans && noScans.textContent === 'No scans yet') {
        noScans.remove();
    }
    
    const html = `
        <li class="px-6 py-3 flex items-center justify-between">
            <div class="flex items-center">
                <span class="w-2 h-2 rounded-full ${success ? 'bg-green-500' : 'bg-red-500'} mr-3"></span>
                <span class="text-sm font-medium text-gray-900">${name}</span>
            </div>
            <span class="text-sm text-gray-500">${time}</span>
        </li>
    `;
    list.insertAdjacentHTML('afterbegin', html);
}

function playBeep() {
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioCtx.createOscillator();
    const gainNode = audioCtx.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioCtx.destination);
    
    oscillator.frequency.value = 800;
    oscillator.type = 'sine';
    gainNode.gain.value = 0.1;
    
    oscillator.start();
    setTimeout(() => oscillator.stop(), 100);
}
</script>
@endpush
@endsection
