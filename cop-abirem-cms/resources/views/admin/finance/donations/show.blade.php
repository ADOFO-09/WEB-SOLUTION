@extends('layouts.admin')

@section('title', 'Donation Details')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.donations.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Donation Details</h1>
                <p class="text-sm text-gray-500">{{ $donation->receipt_number ?? $donation->reference_number }}</p>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('admin.donations.receipt', $donation) }}" target="_blank"
               class="inline-flex items-center px-4 py-2 border border-green-600 rounded-md shadow-sm text-sm font-medium text-green-600 bg-white hover:bg-green-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Receipt
            </a>
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
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Donation Info -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Donation Information</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Receipt Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $donation->receipt_number ?? $donation->reference_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($donation->payment_date)->format('F d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1">
                            @if($donation->donation_type == 'cash')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Cash</span>
                            @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">In-Kind</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Amount</dt>
                        <dd class="mt-1 text-2xl font-bold text-gray-900">
                            @if($donation->donation_type == 'cash')
                            GH₵ {{ number_format($donation->amount, 2) }}
                            @else
                            {{ $donation->in_kind_description }}
                            @if($donation->estimated_value)
                            <span class="text-sm text-gray-500">(Est: GH₵ {{ number_format($donation->estimated_value, 2) }})</span>
                            @endif
                            @endif
                        </dd>
                    </div>
                    @if($donation->donation_type == 'cash')
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $donation->payment_method)) }}</dd>
                    </div>
                    @if($donation->payment_reference)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Reference</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $donation->payment_reference }}</dd>
                    </div>
                    @endif
                    @endif
                    @if($donation->project)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Project</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $donation->project->name }}</dd>
                    </div>
                    @endif
                    @if($donation->incomeCategory)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $donation->incomeCategory->name }}</dd>
                    </div>
                    @endif
                </dl>
                @if($donation->notes)
                <div class="mt-6 pt-6 border-t">
                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $donation->notes }}</dd>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Donor Info -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Donor Information</h3>
            </div>
            <div class="p-6">
                @if($donation->is_anonymous)
                <p class="text-gray-500 italic">Anonymous Donation</p>
                @else
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-indigo-600 font-medium">
                            {{ $donation->member ? substr($donation->member->first_name, 0, 1) . substr($donation->member->last_name, 0, 1) : substr($donation->donor_name ?? 'D', 0, 2) }}
                        </span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">{{ $donation->member->full_name ?? $donation->donor_name ?? 'Unknown' }}</p>
                        @if($donation->member)
                        <p class="text-xs text-gray-500">{{ $donation->member->member_id }}</p>
                        @endif
                        @if($donation->donor_phone)
                        <p class="text-xs text-gray-500">{{ $donation->donor_phone }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Record Info -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Record Information</h3>
            </div>
            <div class="p-6 space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Recorded By</span>
                    <span class="text-gray-900">{{ $donation->recordedBy->name ?? 'System' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Created</span>
                    <span class="text-gray-900">{{ $donation->created_at->format('M d, Y g:i A') }}</span>
                </div>
                @if($donation->updated_at != $donation->created_at)
                <div class="flex justify-between">
                    <span class="text-gray-500">Last Updated</span>
                    <span class="text-gray-900">{{ $donation->updated_at->format('M d, Y g:i A') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
