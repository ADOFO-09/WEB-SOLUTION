@extends('layouts.admin')

@section('title', 'Pledge Report')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.reports.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Pledge Report</h1>
        </div>
        <form action="" method="GET">
            <select name="status" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>
                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </form>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-3xl font-bold text-gray-900">GH₵ {{ number_format($summary['total_pledged'], 2) }}</p>
            <p class="text-sm text-gray-500">Total Pledged</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-3xl font-bold text-green-600">GH₵ {{ number_format($summary['total_paid'], 2) }}</p>
            <p class="text-sm text-gray-500">Total Paid</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $summary['active'] }}</p>
            <p class="text-sm text-gray-500">Active</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-3xl font-bold text-green-600">{{ $summary['fulfilled'] }}</p>
            <p class="text-sm text-gray-500">Fulfilled</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-3xl font-bold text-red-600">{{ $summary['overdue'] }}</p>
            <p class="text-sm text-gray-500">Overdue</p>
        </div>
    </div>

    <!-- Pledges List -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold">Pledges ({{ $pledges->count() }})</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Pledged</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Paid</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($pledges as $pledge)
                    <tr>
                        <td class="px-4 py-3">{{ $pledge->member->full_name ?? 'Unknown' }}</td>
                        <td class="px-4 py-3 text-sm">{{ Str::limit($pledge->purpose, 30) }}</td>
                        <td class="px-4 py-3 text-right font-medium">GH₵ {{ number_format($pledge->total_amount, 2) }}</td>
                        <td class="px-4 py-3 text-right text-green-600">GH₵ {{ number_format($pledge->amount_paid, 2) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="h-2 rounded-full bg-green-500" style="width: {{ $pledge->progress_percentage }}%"></div>
                                </div>
                                <span class="text-xs">{{ $pledge->progress_percentage }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                {{ $pledge->status == 'active' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $pledge->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $pledge->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($pledge->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">No pledges found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
