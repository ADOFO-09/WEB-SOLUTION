@extends('layouts.admin')

@section('title', 'Offering Details')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.offerings.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Offering Details</h1>
        </div>
        @can('finance.edit')
        <a href="{{ route('admin.offerings.edit', $offering) }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            Edit
        </a>
        @endcan
    </div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-8 text-white">
            <div class="text-center">
                <p class="text-blue-100 text-sm">Reference Number</p>
                <h2 class="text-2xl font-bold">{{ $offering->reference_number }}</h2>
                <p class="mt-4 text-4xl font-bold">GH₵ {{ number_format($offering->amount, 2) }}</p>
                <p class="text-blue-100">{{ $offering->incomeCategory->name ?? 'General Offering' }}</p>
            </div>
        </div>

        <!-- Details -->
        <div class="p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Contributor</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">
                        @if($offering->is_anonymous)
                            <span class="text-gray-400 italic">Anonymous</span>
                        @elseif($offering->member)
                            <a href="{{ route('admin.members.show', $offering->member) }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $offering->member->full_name }}
                            </a>
                        @else
                            Unknown
                        @endif
                    </dd>
                </div>

                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Category</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $offering->incomeCategory->name ?? 'Uncategorized' }}</dd>
                </div>

                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Payment Date</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $offering->payment_date->format('F d, Y') }}</dd>
                </div>

                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Payment Method</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $offering->payment_method)) }}</dd>
                </div>

                @if($offering->session)
                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Service Session</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">
                        <a href="{{ route('admin.attendance.show', $offering->session) }}" class="text-indigo-600 hover:text-indigo-900">
                            {{ $offering->session->serviceType->name ?? 'Service' }} - {{ $offering->session->service_date->format('M d, Y') }}
                        </a>
                    </dd>
                </div>
                @endif

                @if($offering->payment_reference)
                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Payment Reference</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $offering->payment_reference }}</dd>
                </div>
                @endif

                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Recorded By</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $offering->recordedBy->name ?? 'System' }}</dd>
                </div>
            </dl>

            @if($offering->notes)
            <div class="mt-4 pt-4 border-t">
                <dt class="text-sm text-gray-500">Notes</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $offering->notes }}</dd>
            </div>
            @endif

            <div class="mt-4 pt-4 border-t text-xs text-gray-500">
                Created: {{ $offering->created_at->format('M d, Y g:i A') }}
            </div>
        </div>
    </div>
</div>
@endsection
