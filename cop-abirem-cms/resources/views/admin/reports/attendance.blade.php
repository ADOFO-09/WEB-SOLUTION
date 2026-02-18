@extends('layouts.admin')

@section('title', 'Attendance Report')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.reports.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Attendance Report</h1>
        </div>
        <form action="" method="GET" class="flex items-center space-x-2">
            <select name="year" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="service_type_id" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                <option value="">All Services</option>
                @foreach($serviceTypes as $type)
                    <option value="{{ $type->id }}" {{ $serviceTypeId == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
        </form>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-gray-900">{{ $totals['sessions'] }}</p>
            <p class="text-sm text-gray-500">Total Sessions</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-blue-600">{{ number_format($totals['total_attendance']) }}</p>
            <p class="text-sm text-gray-500">Total Attendance</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-green-600">{{ $totals['average'] }}</p>
            <p class="text-sm text-gray-500">Average per Service</p>
        </div>
    </div>

    <!-- By Service Type -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold">By Service Type</h3></div>
        <div class="p-6">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Sessions</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Members</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Visitors</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Average</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($byServiceType as $item)
                    <tr>
                        <td class="px-4 py-3">{{ $item->serviceType->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-3 text-right">{{ $item->sessions }}</td>
                        <td class="px-4 py-3 text-right text-blue-600 font-medium">{{ number_format($item->members) }}</td>
                        <td class="px-4 py-3 text-right text-green-600">{{ number_format($item->visitors) }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ $item->sessions > 0 ? round(($item->members + $item->visitors) / $item->sessions) : 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Monthly Trend -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold">Monthly Trend - {{ $year }}</h3></div>
        <div class="p-6">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Sessions</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Members</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Visitors</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Avg/Session</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($monthlyTrend as $data)
                    <tr>
                        <td class="px-4 py-3">{{ $data['month'] }}</td>
                        <td class="px-4 py-3 text-right">{{ $data['sessions'] }}</td>
                        <td class="px-4 py-3 text-right text-blue-600">{{ number_format($data['members']) }}</td>
                        <td class="px-4 py-3 text-right text-green-600">{{ number_format($data['visitors']) }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ $data['average'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
