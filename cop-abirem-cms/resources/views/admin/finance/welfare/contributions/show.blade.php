@extends('layouts.admin')

@section('title', 'Welfare Contribution')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.welfare.contributions.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Welfare Contribution</h1>
    </div>
@endsection

@section('content')
    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 rounded-md p-4">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Reference</p>
                    <p class="text-lg font-bold text-gray-900">{{ $welfareContribution->reference_number }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if(auth()->user()->hasPermission('welfare.edit'))
                    <a href="{{ route('admin.welfare.contributions.edit', $welfareContribution) }}"
                       class="inline-flex items-center px-3 py-1.5 border border-yellow-300 rounded-md text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100">
                        Edit
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('welfare.delete'))
                    <form method="POST" action="{{ route('admin.welfare.contributions.destroy', $welfareContribution) }}"
                          onsubmit="return confirm('Delete this contribution?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-3 py-1.5 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100">
                            Delete
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <dl class="divide-y divide-gray-200">
                <div class="px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Member</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $welfareContribution->member?->full_name ?? '—' }}
                        @if($welfareContribution->member)
                        <span class="text-gray-400 ml-1">({{ $welfareContribution->member->member_id }})</span>
                        @endif
                    </dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Period</dt>
                    <dd class="text-sm text-gray-900 col-span-2">
                        {{ date('F', mktime(0,0,0,$welfareContribution->period_month,1)) }} {{ $welfareContribution->period_year }}
                    </dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Amount</dt>
                    <dd class="text-sm font-bold text-green-600 col-span-2">
                        {{ $currencySymbol }} {{ number_format($welfareContribution->amount, 2) }}
                    </dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Payment Date</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $welfareContribution->payment_date->format('M d, Y') }}</dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                    <dd class="text-sm text-gray-900 col-span-2">
                        {{ $welfareContribution->payment_method ? ucfirst(str_replace('_', ' ', $welfareContribution->payment_method)) : '—' }}
                    </dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Received By</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $welfareContribution->receivedBy?->name ?? '—' }}</dd>
                </div>
                @if($welfareContribution->notes)
                <div class="px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $welfareContribution->notes }}</dd>
                </div>
                @endif
                <div class="px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Recorded At</dt>
                    <dd class="text-sm text-gray-500 col-span-2">{{ $welfareContribution->created_at->format('M d, Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>
@endsection
