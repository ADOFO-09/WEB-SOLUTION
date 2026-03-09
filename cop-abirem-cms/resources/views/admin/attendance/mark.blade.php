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
        <div class="mt-4 sm:mt-0 flex space-x-3">
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
@endpush
@endsection
