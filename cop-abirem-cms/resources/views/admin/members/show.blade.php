@extends('layouts.admin')

@section('title', $member->full_name)

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.members.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $member->full_name }}</h1>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            {{-- Biometric Enrollment --}}
            <a href="{{ route('admin.members.biometric', $member) }}"
               class="inline-flex items-center px-4 py-2 border rounded-md shadow-sm text-sm font-medium
                      {{ $member->biometric_enrolled
                           ? 'border-green-300 text-green-700 bg-green-50 hover:bg-green-100'
                           : 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50' }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                </svg>
                {{ $member->biometric_enrolled ? 'Biometric Enrolled' : 'Enroll Biometric' }}
            </a>
            <a href="{{ route('admin.members.qrcode', $member) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                QR Code
            </a>
            <a href="{{ route('admin.members.card', $member) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"/>
                </svg>
                Print Card
            </a>
            @can('members.edit')
            <a href="{{ route('admin.members.edit', $member) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column - Profile -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Profile Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="h-24" style="background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8a 60%, #d4af37 100%);"></div>
            <div class="px-6 pb-6">
                <div class="-mt-12 flex justify-center">
                    @if($member->photo_path)
                        <img class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg" 
                             src="{{ asset('storage/' . $member->photo_path) }}" 
                             alt="{{ $member->full_name }}">
                    @else
                        <div class="h-24 w-24 rounded-full border-4 border-white flex items-center justify-center shadow-lg" style="background:linear-gradient(135deg,#1e3a5f,#2d5a8a);">
                            <span class="text-white font-bold text-2xl">
                                {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="text-center mt-4">
                    <h2 class="text-xl font-bold text-gray-900">{{ $member->full_name }}</h2>
                    <p class="text-sm text-gray-500 font-mono">{{ $member->member_id }}</p>
                    
                    @php
                        $statusColors = [
                            'active' => 'bg-green-100 text-green-800',
                            'inactive' => 'bg-red-100 text-red-800',
                            'transferred_out' => 'bg-yellow-100 text-yellow-800',
                            'transferred_in' => 'bg-blue-100 text-blue-800',
                            'deceased' => 'bg-gray-100 text-gray-800',
                        ];
                    @endphp
                    <span class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$member->membership_status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst(str_replace('_', ' ', $member->membership_status)) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics (This Year)</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Tithes</span>
                    <span class="font-semibold text-gray-900">GH₵ {{ number_format($stats['total_tithes'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Offerings</span>
                    <span class="font-semibold text-gray-900">GH₵ {{ number_format($stats['total_offerings'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Attendance Rate (90 days)</span>
                    <span class="font-semibold text-gray-900">{{ $stats['attendance_rate'] }}%</span>
                </div>
            </div>
        </div>

        <!-- Ministries -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ministries</h3>
                @can('members.edit')
                <a href="{{ route('admin.members.edit', $member) }}#ministries" class="text-indigo-600 hover:text-indigo-900 text-sm">
                    Manage
                </a>
                @endcan
            </div>
            @if($member->activeMinistries->count() > 0)
                <div class="space-y-2">
                    @foreach($member->activeMinistries as $ministry)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <span class="text-gray-700">{{ $ministry->name }}</span>
                        <span class="text-xs px-2 py-1 rounded-full 
                            {{ $ministry->pivot->role == 'leader' ? 'bg-yellow-100 text-yellow-800' : 
                               ($ministry->pivot->role == 'assistant_leader' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst(str_replace('_', ' ', $ministry->pivot->role)) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No ministry assignments</p>
            @endif
        </div>

        <!-- Family Relationships -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Family</h3>
                @can('members.edit')
                <a href="{{ route('admin.members.family', $member) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                    Manage
                </a>
                @endcan
            </div>
            @if($member->familyRelationships->count() > 0)
                <div class="space-y-2">
                    @foreach($member->familyRelationships as $relationship)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <a href="{{ route('admin.members.show', $relationship->relatedMember) }}" 
                               class="text-gray-900 hover:text-indigo-600">
                                {{ $relationship->relatedMember->full_name }}
                            </a>
                            <span class="text-xs text-gray-500 ml-2">({{ $relationship->relationship_label }})</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No family relationships linked</p>
            @endif
        </div>
    </div>

    <!-- Right Column - Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Personal Information -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $member->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $member->date_of_birth?->format('F d, Y') }} 
                            @if($member->age)<span class="text-gray-500">({{ $member->age }} years old)</span>@endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Gender</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($member->gender) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Marital Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($member->marital_status) }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Contact Information</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Primary Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="tel:{{ $member->phone_primary }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $member->phone_primary }}
                            </a>
                        </dd>
                    </div>
                    @if($member->phone_secondary)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Secondary Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="tel:{{ $member->phone_secondary }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $member->phone_secondary }}
                            </a>
                        </dd>
                    </div>
                    @endif
                    @if($member->email)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="mailto:{{ $member->email }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $member->email }}
                            </a>
                        </dd>
                    </div>
                    @endif
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $member->address ?? 'N/A' }}
                            @if($member->city || $member->region)
                                <br>{{ implode(', ', array_filter([$member->city, $member->region])) }}
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Work & Emergency -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Work & Emergency Contact</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Occupation</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $member->occupation ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Employer</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $member->employer ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Emergency Contact</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $member->emergency_contact_name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Emergency Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($member->emergency_contact_phone)
                                <a href="tel:{{ $member->emergency_contact_phone }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $member->emergency_contact_phone }}
                                </a>
                            @else
                                N/A
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Church Information -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Church Information</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date Joined</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $member->date_joined?->format('F d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Baptism Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $member->baptism_date?->format('F d, Y') ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Baptism Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $member->baptism_type)) }}</dd>
                    </div>
                    @if($member->previous_church)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Previous Church</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $member->previous_church }}</dd>
                    </div>
                    @endif
                </dl>
                @if($member->notes)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $member->notes }}</dd>
                </div>
                @endif
            </div>
        </div>

        <!-- Recent Tithes -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Recent Tithes</h3>
                <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month For</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($member->tithes as $tithe)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $tithe->payment_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                GH₵ {{ number_format($tithe->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $tithe->month_for->format('F Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $tithe->receipt_number }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                No tithe records found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Recent Attendance</h3>
                <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($member->attendanceRecords as $record)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->session->service_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $record->session->serviceType->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $record->check_in_time->format('g:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($record->is_late)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Late
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        On Time
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                No attendance records found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Meta Information -->
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                <span>Created: {{ $member->created_at->format('M d, Y g:i A') }} by {{ $member->createdBy->name ?? 'System' }}</span>
                @if($member->updated_by)
                <span>• Last updated: {{ $member->updated_at->format('M d, Y g:i A') }} by {{ $member->updatedBy->name ?? 'System' }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
