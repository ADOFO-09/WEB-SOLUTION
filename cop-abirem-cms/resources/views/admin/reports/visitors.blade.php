@extends('layouts.admin')

@section('title', 'Visitor Report')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.reports.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Visitor Report</h1>
        </div>
        <form action="" method="GET">
            <select name="year" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                @forelse($financialYears as $fy)
                    <option value="{{ $fy->start_date->year }}"
                        {{ $year == $fy->start_date->year ? 'selected' : '' }}>
                        {{ $fy->name }}{{ $fy->is_active ? ' ★' : '' }}
                    </option>
                @empty
                    <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                @endforelse
            </select>
        </form>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-gray-900">{{ $totals['total'] }}</p>
            <p class="text-sm text-gray-500">Total Visitors</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-green-600">{{ $totals['converted'] }}</p>
            <p class="text-sm text-gray-500">Converted</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-yellow-600">{{ $totals['pending'] }}</p>
            <p class="text-sm text-gray-500">Pending Follow-up</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-blue-600">{{ $totals['conversion_rate'] }}%</p>
            <p class="text-sm text-gray-500">Conversion Rate</p>
        </div>
    </div>

    <!-- Monthly Trend -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold">Monthly Trend</h3></div>
        <div class="p-6">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">New Visitors</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Converted</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($monthlyVisitors as $data)
                    <tr>
                        <td class="px-4 py-3">{{ $data['month'] }}</td>
                        <td class="px-4 py-3 text-right">{{ $data['new'] }}</td>
                        <td class="px-4 py-3 text-right text-green-600 font-medium">{{ $data['converted'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- By Follow-up Status -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold">By Follow-up Status</h3></div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($byFollowUpStatus as $status => $count)
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold">{{ $count }}</p>
                    <p class="text-sm text-gray-500 capitalize">{{ str_replace('_', ' ', $status) }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
