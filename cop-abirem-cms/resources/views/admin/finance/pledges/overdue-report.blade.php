@extends('layouts.admin')

@section('title', 'Overdue Pledges Report')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.pledges.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Overdue Pledges Report</h1>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-400">
            <div class="text-sm font-medium text-gray-500">Total Overdue</div>
            <div class="text-2xl font-bold text-red-600">{{ $currencySymbol }} {{ number_format($totalOverdue, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Overdue Pledges</div>
            <div class="text-2xl font-bold text-gray-900">{{ $pledges->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Average Balance</div>
            <div class="text-2xl font-bold text-orange-600">
                {{ $currencySymbol }} {{ $pledges->count() > 0 ? number_format($totalOverdue / $pledges->count(), 2) : '0.00' }}
            </div>
        </div>
    </div>

    <!-- Overdue Pledges Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Overdue Pledge Details</h2>
            <span class="text-sm text-red-500 font-medium">{{ $pledges->count() }} pledge(s) overdue</span>
        </div>
        @if($pledges->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pledged</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Days Overdue</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pledges as $pledge)
                    @php $daysOverdue = now()->diffInDays($pledge->due_date, false) * -1; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-mono text-gray-500">{{ $pledge->reference_number }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $pledge->member?->full_name ?? 'Unknown' }}
                            @if($pledge->member)
                            <div class="text-xs text-gray-400">{{ $pledge->member->phone ?? '' }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $pledge->project?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-red-600 font-medium">{{ $pledge->due_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-700">{{ $currencySymbol }} {{ number_format($pledge->amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-green-600">{{ $currencySymbol }} {{ number_format($pledge->amount_paid, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right font-bold text-red-600">{{ $currencySymbol }} {{ number_format($pledge->balance, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $daysOverdue > 90 ? 'bg-red-100 text-red-800' : ($daysOverdue > 30 ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $daysOverdue }} days
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.pledges.show', $pledge) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-sm font-semibold text-gray-700">Total Outstanding</td>
                        <td class="px-6 py-4 text-sm text-right font-bold text-red-600">{{ $currencySymbol }} {{ number_format($totalOverdue, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm font-medium text-gray-500">No overdue pledges — great job!</p>
        </div>
        @endif
    </div>
</div>
@endsection
