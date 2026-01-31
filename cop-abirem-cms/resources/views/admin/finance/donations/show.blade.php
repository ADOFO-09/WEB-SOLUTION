@extends('layouts.admin')

@section('title', 'Donation Details')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.donations.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Donation Details</h1>
        </div>
        <div class="flex space-x-3">
            @if($donation->donation_type == 'cash')
            <a href="{{ route('admin.donations.receipt', $donation) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Receipt
            </a>
            @endif
            @can('finance.edit')
            <a href="{{ route('admin.donations.edit', $donation) }}" 
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
        <div class="bg-gradient-to-r {{ $donation->donation_type == 'cash' ? 'from-green-500 to-green-600' : 'from-purple-500 to-purple-600' }} px-6 py-8 text-white">
            <div class="text-center">
                <p class="text-white/80 text-sm">Receipt Number</p>
                <h2 class="text-2xl font-bold">{{ $donation->receipt_number }}</h2>
                @if($donation->donation_type == 'cash')
                <p class="mt-4 text-4xl font-bold">GH₵ {{ number_format($donation->amount, 2) }}</p>
                <p class="text-white/80">Cash Donation</p>
                @else
                <p class="mt-4 text-2xl font-bold">In-Kind Donation</p>
                @if($donation->estimated_value)
                <p class="text-white/80">Est. Value: GH₵ {{ number_format($donation->estimated_value, 2) }}</p>
                @endif
                @endif
            </div>
        </div>

        <!-- Details -->
        <div class="p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Donor</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">
                        @if($donation->is_anonymous)
                            <span class="text-gray-400 italic">Anonymous</span>
                        @elseif($donation->member)
                            <a href="{{ route('admin.members.show', $donation->member) }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $donation->member->full_name }}
                            </a>
                        @else
                            {{ $donation->donor_name ?? 'Unknown' }}
                            @if($donation->donor_phone)
                            <span class="block text-xs text-gray-500">{{ $donation->donor_phone }}</span>
                            @endif
                        @endif
                    </dd>
                </div>

                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Date</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $donation->payment_date->format('F d, Y') }}</dd>
                </div>

                @if($donation->donation_type == 'cash')
                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Payment Method</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $donation->payment_method)) }}</dd>
                </div>
                @endif

                @if($donation->project)
                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Project</dt>
                    <dd class="mt-1 text-sm font-medium text-indigo-600">{{ $donation->project->name }}</dd>
                </div>
                @endif

                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Reference Number</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $donation->reference_number }}</dd>
                </div>

                <div class="border-b pb-3">
                    <dt class="text-sm text-gray-500">Recorded By</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $donation->recordedBy->name ?? 'System' }}</dd>
                </div>
            </dl>

            @if($donation->donation_type == 'in_kind' && $donation->in_kind_description)
            <div class="mt-4 pt-4 border-t">
                <dt class="text-sm text-gray-500">Description of Items</dt>
                <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded">{{ $donation->in_kind_description }}</dd>
            </div>
            @endif

            @if($donation->notes)
            <div class="mt-4 pt-4 border-t">
                <dt class="text-sm text-gray-500">Notes</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $donation->notes }}</dd>
            </div>
            @endif

            <div class="mt-4 pt-4 border-t text-xs text-gray-500">
                Created: {{ $donation->created_at->format('M d, Y g:i A') }}
            </div>
        </div>
    </div>
</div>
@endsection
