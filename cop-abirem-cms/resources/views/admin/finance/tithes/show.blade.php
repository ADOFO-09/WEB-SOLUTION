@extends('layouts.admin')

@section('title', 'Tithe Details')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.tithes.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Tithe Details</h1>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.tithes.receipt', $tithe) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Receipt
            </a>
            @can('finance.edit')
            <a href="{{ route('admin.tithes.edit', $tithe) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                Edit
            </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-8 text-white">
            <div class="text-center">
                <p class="text-green-100 text-sm">Receipt Number</p>
                <h2 class="text-2xl font-bold">{{ $tithe->receipt_number }}</h2>
                <p class="mt-4 text-4xl font-bold">{{ $currencySymbol }} {{ number_format($tithe->amount, 2) }}</p>
                <p class="text-green-100">Tithe Payment</p>
            </div>
        </div>

        <!-- Details -->
        <div class="p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($tithe->member)
                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Member</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">
                        <a href="{{ route('admin.members.show', $tithe->member) }}" class="text-indigo-600 hover:text-indigo-900">
                            {{ $tithe->member->full_name }}
                        </a>
                    </dd>
                </div>
                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Member ID</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $tithe->member->member_id }}</dd>
                </div>
                @else
                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Tithe Type</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Session Tithe</span>
                    </dd>
                </div>
                @if($tithe->attendanceSession)
                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Service</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $tithe->attendanceSession->serviceType->name ?? 'Service' }}</dd>
                </div>
                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Service Date</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $tithe->attendanceSession->service_date->format('F d, Y') }}</dd>
                </div>
                @endif
                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Collection Note</dt>
                    <dd class="mt-1 text-sm text-gray-500 italic">Bulk collection — not linked to individual member</dd>
                </div>
                @endif

                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Month For</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $tithe->month_for_formatted }}</dd>
                </div>

                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Payment Date</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $tithe->payment_date->format('F d, Y') }}</dd>
                </div>

                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Payment Method</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $tithe->payment_method)) }}</dd>
                </div>

                @if($tithe->payment_reference)
                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Payment Reference</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $tithe->payment_reference }}</dd>
                </div>
                @endif

                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Reference Number</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $tithe->reference_number }}</dd>
                </div>

                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Recorded By</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $tithe->recordedBy->name ?? 'System' }}</dd>
                </div>

                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">SMS Notification</dt>
                    <dd class="mt-1">
                        @if($tithe->sms_sent)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Sent
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            Not sent
                        </span>
                        @endif
                    </dd>
                </div>
            </dl>

            @if($tithe->notes)
            <div class="mt-4 pt-4 border-t">
                <dt class="text-sm text-gray-500">Notes</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $tithe->notes }}</dd>
            </div>
            @endif

            <div class="mt-4 pt-4 border-t text-xs text-gray-500">
                Created: {{ $tithe->created_at->format('M d, Y g:i A') }}
                @if($tithe->updated_at->ne($tithe->created_at))
                • Updated: {{ $tithe->updated_at->format('M d, Y g:i A') }}
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 flex justify-between">
        @if($tithe->member)
        <a href="{{ route('admin.tithes.member-history', $tithe->member) }}"
           class="text-indigo-600 hover:text-indigo-900 text-sm">
            ← View {{ $tithe->member->first_name }}'s Tithe History
        </a>
        <a href="{{ route('admin.tithes.create', ['member_id' => $tithe->member_id]) }}"
           class="text-indigo-600 hover:text-indigo-900 text-sm">
            Record Another Tithe for {{ $tithe->member->first_name }} →
        </a>
        @else
        <a href="{{ route('admin.tithes.index') }}"
           class="text-indigo-600 hover:text-indigo-900 text-sm">
            ← Back to All Tithes
        </a>
        <a href="{{ route('admin.tithes.session.create') }}"
           class="text-indigo-600 hover:text-indigo-900 text-sm">
            Record Another Session Tithe →
        </a>
        @endif
    </div>
</div>
@endsection
