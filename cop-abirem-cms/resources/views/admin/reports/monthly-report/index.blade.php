@extends('layouts.admin')

@section('title', 'Monthly Reports')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.reports.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Monthly Reports</h1>
        </div>
        <a href="{{ route('admin.reports.monthly-report.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Report
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg">{{ session('info') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prepared by</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reports as $report)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $report->period_label }}</td>
                    <td class="px-6 py-4">
                        @if($report->status === 'submitted')
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Submitted</span>
                        @else
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full">Draft</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $report->creator?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $report->submitted_at ? $report->submitted_at->format('d M Y') : '—' }}
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.reports.monthly-report.show', $report) }}"
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</a>
                        @if($report->status === 'draft')
                        <a href="{{ route('admin.reports.monthly-report.edit', $report) }}"
                           class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">Edit</a>
                        @endif
                        <a href="{{ route('admin.reports.monthly-report.pdf', $report) }}"
                           class="text-red-600 hover:text-red-800 text-sm font-medium">PDF</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        No monthly reports yet.
                        <a href="{{ route('admin.reports.monthly-report.create') }}" class="text-blue-600 hover:underline ml-1">Create the first one.</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($reports->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $reports->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
