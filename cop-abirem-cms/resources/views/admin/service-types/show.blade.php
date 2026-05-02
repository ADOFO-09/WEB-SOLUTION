@extends('layouts.admin')

@section('title', $serviceType->name)

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.service-types.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $serviceType->name }}</h1>
        </div>
        @can('attendance.edit')
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.service-types.edit', $serviceType) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white"
               style="background-color:#1e3a5f;">
                Edit Service Type
            </a>
        </div>
        @endcan
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Stats cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sessions</div>
            <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($stats['total_sessions']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Attendance</div>
            <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($stats['total_attendance']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Per Session</div>
            <div class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['avg_attendance'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Details card --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">Details</h3>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $serviceType->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $serviceType->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Day</dt>
                        <dd class="font-medium text-gray-900">{{ $serviceType->day_of_week ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Default Time</dt>
                        <dd class="font-medium text-gray-900">{{ $serviceType->default_start_time?->format('g:i A') ?? '—' }}</dd>
                    </div>
                </dl>

                @if($serviceType->description)
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-sm text-gray-600">{{ $serviceType->description }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Recent sessions --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Recent Sessions</h3>
                </div>
                @if($serviceType->attendanceSessions->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Theme</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Members</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Visitors</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($serviceType->attendanceSessions as $session)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-700">{{ $session->service_date->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $session->theme ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ $session->total_members ?? 0 }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ $session->total_visitors ?? 0 }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900">{{ $session->total_attendance ?? 0 }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ $session->status === 'closed' ? 'bg-gray-100 text-gray-700' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="px-6 py-8 text-center text-sm text-gray-400">No sessions recorded yet.</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
