@extends('layouts.admin')

@section('title', $project->name)

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.finance.projects.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h1>
        </div>
        @can('finance.manage')
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('admin.finance.projects.edit', $project) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white"
               style="background-color:#1e3a5f;">
                Edit Project
            </a>
        </div>
        @endcan
    </div>
@endsection

@section('content')

@php
    $statusColors = [
        'planned'   => 'bg-gray-100 text-gray-700',
        'active'    => 'bg-green-100 text-green-800',
        'completed' => 'bg-blue-100 text-blue-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];
@endphp

<div class="space-y-6">

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Target</div>
            <div class="mt-1 text-2xl font-bold text-gray-900">GH₵{{ number_format($project->target_amount, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Raised</div>
            <div class="mt-1 text-2xl font-bold text-green-700">GH₵{{ number_format($raised, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</div>
            <div class="mt-1 text-2xl font-bold text-red-600">GH₵{{ number_format($project->balance, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</div>
            <div class="mt-2">
                <div class="flex items-center space-x-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full" style="width:{{ $project->progress_percentage }}%;background-color:#d4af37;"></div>
                    </div>
                    <span class="text-sm font-bold text-gray-700">{{ $project->progress_percentage }}%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Project details --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">Project Details</h3>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$project->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Start Date</dt>
                        <dd class="font-medium text-gray-900">{{ $project->start_date->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">End Date</dt>
                        <dd class="font-medium text-gray-900">{{ $project->end_date?->format('M d, Y') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Donations</dt>
                        <dd class="font-medium text-gray-900">{{ $donationCount }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Pledges</dt>
                        <dd class="font-medium text-gray-900">{{ $pledgeCount }}</dd>
                    </div>
                </dl>

                @if($project->description)
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-sm text-gray-600">{{ $project->description }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Recent donations --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Recent Donations</h3>
                </div>
                @if($recentDonations->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Donor</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentDonations as $donation)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-700">{{ $donation->payment_date->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-gray-900">
                                    @if($donation->member)
                                        <a href="{{ route('admin.members.show', $donation->member) }}" class="text-indigo-600 hover:underline">
                                            {{ $donation->member->full_name }}
                                        </a>
                                    @elseif($donation->is_anonymous)
                                        <span class="text-gray-400 italic">Anonymous</span>
                                    @else
                                        {{ $donation->donor_name ?? '—' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-green-700">GH₵{{ number_format($donation->amount, 2) }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $donation->receipt_number }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="px-6 py-8 text-center text-sm text-gray-400">No donations yet.</p>
                @endif
            </div>

            {{-- Recent pledges --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Pledges</h3>
                </div>
                @if($recentPledges->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Paid</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentPledges as $pledge)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.members.show', $pledge->member) }}" class="text-indigo-600 hover:underline">
                                        {{ $pledge->member->full_name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900">GH₵{{ number_format($pledge->total_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right text-green-700">GH₵{{ number_format($pledge->amount_paid, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ $pledge->status === 'completed' ? 'bg-green-100 text-green-800' :
                                           ($pledge->status === 'active' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700') }}">
                                        {{ ucfirst($pledge->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="px-6 py-8 text-center text-sm text-gray-400">No pledges yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
