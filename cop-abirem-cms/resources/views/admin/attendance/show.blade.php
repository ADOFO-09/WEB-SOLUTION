@extends('layouts.admin')

@section('title', 'Session Details')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.attendance.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $attendance->serviceType->name ?? 'Service' }}</h1>
                <p class="text-sm text-gray-500">{{ $attendance->formatted_date }}</p>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            @if($attendance->is_open)
            <a href="{{ route('admin.attendance.mark', $attendance) }}" 
               class="inline-flex items-center px-4 py-2 border border-green-600 rounded-md shadow-sm text-sm font-medium text-green-600 bg-white hover:bg-green-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Continue Marking
            </a>
            <form action="{{ route('admin.attendance.close', $attendance) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                    Close Session
                </button>
            </form>
            @else
            <form action="{{ route('admin.attendance.reopen', $attendance) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Reopen Session
                </button>
            </form>
            @endif
        </div>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Session Details -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Session Info</h3>
                @if($attendance->is_open)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 mr-1 bg-green-400 rounded-full animate-pulse"></span>
                    Open
                </span>
                @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    Closed
                </span>
                @endif
            </div>

            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Service Type</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $attendance->serviceType->name ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Date</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $attendance->service_date->format('M d, Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Time</dt>
                    <dd class="text-sm font-medium text-gray-900">
                        {{ $attendance->start_time?->format('g:i A') }}
                        @if($attendance->end_time) - {{ $attendance->end_time->format('g:i A') }}@endif
                    </dd>
                </div>
                @if($attendance->ministry)
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Ministry</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $attendance->ministry->name }}</dd>
                </div>
                @endif
                @if($attendance->theme)
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Theme</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $attendance->theme }}</dd>
                </div>
                @endif
                @if($attendance->preacher)
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Preacher</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $attendance->preacher }}</dd>
                </div>
                @endif
            </dl>

            @if($attendance->closed_at)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    Closed {{ $attendance->closed_at->format('M d, Y g:i A') }}
                    by {{ $attendance->closedBy->name ?? 'System' }}
                </p>
            </div>
            @endif
        </div>

        <!-- Statistics -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-3 bg-indigo-50 rounded-lg">
                    <div class="text-2xl font-bold text-indigo-600">{{ $stats['total'] }}</div>
                    <div class="text-xs text-indigo-600">Total</div>
                </div>
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['members'] }}</div>
                    <div class="text-xs text-blue-600">Members</div>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['visitors'] }}</div>
                    <div class="text-xs text-green-600">Visitors</div>
                </div>
                <div class="text-center p-3 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['late'] }}</div>
                    <div class="text-xs text-yellow-600">Late</div>
                </div>
            </div>
            @if($attendance->total_children)
            <div class="mt-4 text-center p-3 bg-purple-50 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ $attendance->total_children }}</div>
                <div class="text-xs text-purple-600">Children</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Attendance Records</h3>
                <span class="text-sm text-gray-500">{{ $stats['total'] }} records</span>
            </div>

            @if($attendance->records->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($attendance->records as $record)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        @if($record->member && $record->member->photo_path)
                                            <img class="h-8 w-8 rounded-full object-cover" src="{{ asset('storage/' . $record->member->photo_path) }}" alt="">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                                <span class="text-gray-500 text-xs">
                                                    {{ substr($record->attendee_name, 0, 2) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $record->attendee_name }}</p>
                                        @if($record->member)
                                        <p class="text-xs text-gray-500">{{ $record->member->member_id }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $record->member_id ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $record->member_id ? 'Member' : 'Visitor' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $record->check_in_time->format('g:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ucfirst(str_replace('_', ' ', $record->attendance_method)) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($record->is_late)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Late
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    On Time
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <p class="text-gray-500">No attendance records for this session.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
