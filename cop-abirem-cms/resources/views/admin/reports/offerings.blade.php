@extends('layouts.admin')

@section('title', 'Offering Report')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.reports.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Offering Report</h1>
        </div>
        <form action="{{ route('admin.reports.offerings') }}" method="GET" class="flex items-center space-x-2">
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
            <select name="category_id" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </form>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-green-600">{{ $currencySymbol }} {{ number_format($totals['total'], 2) }}</p>
            <p class="text-sm text-gray-500">Total Offerings</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-blue-600">{{ $totals['categories'] }}</p>
            <p class="text-sm text-gray-500">Active Categories</p>
        </div>
    </div>

    <!-- By Category -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold">By Category</h3></div>
        <div class="p-6">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">% of Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($byCategory->filter(fn($c) => $c->total > 0) as $cat)
                    <tr>
                        <td class="px-4 py-3">{{ $cat->name }}</td>
                        <td class="px-4 py-3 text-right font-medium text-green-600">{{ $currencySymbol }} {{ number_format($cat->total, 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ $totals['total'] > 0 ? round(($cat->total / $totals['total']) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($monthlyTrend as $data)
                    <tr>
                        <td class="px-4 py-3">{{ $data['month'] }}</td>
                        <td class="px-4 py-3 text-right font-medium text-green-600">{{ $currencySymbol }} {{ number_format($data['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
