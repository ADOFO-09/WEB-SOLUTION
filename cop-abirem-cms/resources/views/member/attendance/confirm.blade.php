@extends('layouts.member')

@section('title', 'Confirm Attendance')

@section('header')
<h2 class="text-lg font-semibold text-gray-800">Confirm Attendance</h2>
@endsection

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow overflow-hidden">

        {{-- Success indicator --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-8 text-center">
            <div class="w-16 h-16 mx-auto bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-white font-bold text-xl">QR Code Verified</h3>
            <p class="text-blue-100 text-sm mt-1">Tap Confirm to record your attendance</p>
        </div>

        {{-- Session details --}}
        <div class="px-6 py-5 space-y-4">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Service</p>
                <p class="text-lg font-bold text-gray-900">{{ $session->serviceType->name ?? 'Service' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Date</p>
                    <p class="text-sm font-medium text-gray-800">{{ $session->service_date->format('l, ' . $dateFormat) }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Time</p>
                    <p class="text-sm font-medium text-gray-800">
                        {{ $session->start_time?->format($timeFormat) ?? '—' }}
                    </p>
                </div>
            </div>

            @if($session->theme)
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Theme</p>
                <p class="text-sm text-gray-700">{{ $session->theme }}</p>
            </div>
            @endif

            {{-- Member info --}}
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center">
                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($member->first_name, 0, 1) . substr($member->last_name, 0, 1)) }}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-gray-900">{{ $member->full_name }}</p>
                    <p class="text-xs text-gray-400">{{ $member->member_id }}</p>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="px-6 pb-6 flex space-x-3">
            <form action="{{ route('member.attendance.record') }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="session_id" value="{{ $session->id }}">
                <button type="submit"
                        class="w-full py-3 bg-indigo-600 text-white rounded-lg font-semibold text-sm hover:bg-indigo-700 transition">
                    Confirm Attendance
                </button>
            </form>
            <a href="{{ route('member.attendance.scan') }}"
               class="flex-1 py-3 border border-gray-300 text-gray-600 rounded-lg font-medium text-sm text-center hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>

    </div>

    <p class="mt-4 text-center text-xs text-gray-400">
        Your attendance will be recorded at {{ now()->format($timeFormat) }} on {{ now()->format($dateFormat) }}.
    </p>
</div>
@endsection
