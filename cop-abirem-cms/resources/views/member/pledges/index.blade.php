@extends('layouts.member')

@section('title', 'My Pledges')

@section('header')
<h1 class="text-xl font-bold text-gray-900">My Pledges</h1>
@endsection

@section('content')
<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-gray-500 mb-1">Total Pledged</div>
        <div class="text-2xl font-bold text-gray-900">GH₵ {{ number_format($summary['total_pledged'], 2) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-gray-500 mb-1">Amount Paid</div>
        <div class="text-2xl font-bold text-green-600">GH₵ {{ number_format($summary['total_paid'], 2) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-gray-500 mb-1">Remaining</div>
        <div class="text-2xl font-bold text-yellow-600">GH₵ {{ number_format($summary['remaining'], 2) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-gray-500 mb-1">Active / Completed</div>
        <div class="text-2xl font-bold text-gray-900">{{ $summary['active_count'] }} / {{ $summary['completed_count'] }}</div>
    </div>
</div>

<!-- Filter -->
<div class="mb-6">
    <form action="{{ route('member.pledges.index') }}" method="GET" class="flex items-center space-x-2">
        <label class="text-sm text-gray-600">Status:</label>
        <select name="status" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All</option>
            <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
            <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </form>
</div>

<!-- Pledges List -->
<div class="space-y-4">
    @forelse($pledges as $pledge)
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $pledge->project?->name ?? $pledge->incomeCategory?->name ?? 'General Pledge' }}</h3>
                <p class="text-sm text-gray-500">{{ $pledge->pledge_number }}</p>
            </div>
            <div class="mt-2 md:mt-0">
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($pledge->status == 'active') bg-blue-100 text-blue-800
                    @elseif($pledge->status == 'completed') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($pledge->status) }}
                </span>
            </div>
        </div>
        
        <!-- Progress Bar -->
        @php
            $progress = $pledge->total_amount > 0 ? min(100, ($pledge->amount_paid / $pledge->total_amount) * 100) : 0;
        @endphp
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
                <span class="text-gray-600">Progress</span>
                <span class="font-medium">{{ round($progress) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="h-3 rounded-full {{ $pledge->status == 'completed' ? 'bg-green-500' : 'bg-blue-600' }}" style="width: {{ $progress }}%"></div>
            </div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Total Amount</span>
                <p class="font-medium text-gray-900">GH₵ {{ number_format($pledge->total_amount, 2) }}</p>
            </div>
            <div>
                <span class="text-gray-500">Amount Paid</span>
                <p class="font-medium text-green-600">GH₵ {{ number_format($pledge->amount_paid, 2) }}</p>
            </div>
            <div>
                <span class="text-gray-500">Remaining</span>
                <p class="font-medium text-yellow-600">GH₵ {{ number_format($pledge->total_amount - $pledge->amount_paid, 2) }}</p>
            </div>
            <div>
                <span class="text-gray-500">Due Date</span>
                <p class="font-medium text-gray-900">{{ $pledge->due_date?->format('M d, Y') ?? '-' }}</p>
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-t flex justify-between items-center">
            <span class="text-sm text-gray-500">
                Pledge Date: {{ $pledge->pledge_date?->format('M d, Y') ?? $pledge->created_at->format('M d, Y') }}
            </span>
            <a href="{{ route('member.pledges.show', $pledge) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                View Details →
            </a>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-gray-500">No pledges found.</p>
    </div>
    @endforelse
</div>

@if($pledges->hasPages())
<div class="mt-6">
    {{ $pledges->links() }}
</div>
@endif
@endsection
