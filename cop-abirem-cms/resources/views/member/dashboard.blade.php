@extends('layouts.member')

@section('title', 'Dashboard')

@section('header')
<h1 class="text-xl font-bold text-gray-900">Welcome, {{ $member->first_name }}!</h1>
@endsection

@section('content')
<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Total Giving (YTD)</p>
                <p class="text-2xl font-bold text-gray-900">{{ $currencySymbol }} {{ number_format($givingSummary['total_ytd'], 2) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Tithes (YTD)</p>
                <p class="text-2xl font-bold text-gray-900">{{ $currencySymbol }} {{ number_format($givingSummary['tithes_ytd'], 2) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Active Pledges</p>
                <p class="text-2xl font-bold text-gray-900">{{ $pledgeSummary['active_pledges'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Attendance (3 months)</p>
                <p class="text-2xl font-bold text-gray-900">{{ $attendanceCount }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Giving Chart -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Giving (Last 6 Months)</h3>
        <div class="h-64">
            <canvas id="givingChart"></canvas>
        </div>
    </div>

    <!-- Pledge Summary -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pledge Summary</h3>
        @if($pledgeSummary['active_pledges'] > 0)
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600">Progress</span>
                    <span class="font-medium">{{ $pledgeSummary['total_pledged'] > 0 ? round(($pledgeSummary['total_paid'] / $pledgeSummary['total_pledged']) * 100) : 0 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $pledgeSummary['total_pledged'] > 0 ? min(100, ($pledgeSummary['total_paid'] / $pledgeSummary['total_pledged']) * 100) : 0 }}%"></div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-center">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Total Pledged</p>
                    <p class="text-lg font-bold text-gray-900">{{ $currencySymbol }} {{ number_format($pledgeSummary['total_pledged'], 2) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Amount Paid</p>
                    <p class="text-lg font-bold text-green-600">{{ $currencySymbol }} {{ number_format($pledgeSummary['total_paid'], 2) }}</p>
                </div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-3 text-center">
                <p class="text-xs text-yellow-700">Remaining Balance</p>
                <p class="text-xl font-bold text-yellow-600">{{ $currencySymbol }} {{ number_format($pledgeSummary['remaining'], 2) }}</p>
            </div>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p>No active pledges</p>
        </div>
        @endif
        <a href="{{ route('member.pledges.index') }}" class="block mt-4 text-center text-blue-600 hover:text-blue-700 text-sm font-medium">
            View All Pledges →
        </a>
    </div>
</div>

<!-- Recent Activity & Upcoming -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <!-- Recent Tithes -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Recent Tithes</h3>
            <a href="{{ route('member.giving.tithes') }}" class="text-blue-600 hover:text-blue-700 text-sm">View All</a>
        </div>
        @forelse($recentTithes as $tithe)
        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
            <div>
                <p class="font-medium text-gray-900">{{ $currencySymbol }} {{ number_format($tithe->amount, 2) }}</p>
                <p class="text-sm text-gray-500">{{ $tithe->payment_date->format($dateFormat) }}</p>
            </div>
            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">{{ ucfirst($tithe->payment_method) }}</span>
        </div>
        @empty
        <p class="text-center text-gray-500 py-4">No tithe records yet</p>
        @endforelse
    </div>

    <!-- Upcoming Pledge Due Dates -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Upcoming Pledge Due Dates</h3>
            <a href="{{ route('member.pledges.index') }}" class="text-blue-600 hover:text-blue-700 text-sm">View All</a>
        </div>
        @forelse($upcomingPledges as $pledge)
        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
            <div>
                <p class="font-medium text-gray-900">{{ $pledge->project?->name ?? 'General Pledge' }}</p>
                <p class="text-sm text-gray-500">Due: {{ $pledge->due_date->format($dateFormat) }}</p>
            </div>
            <div class="text-right">
                <p class="font-medium text-gray-900">{{ $currencySymbol }} {{ number_format($pledge->total_amount - $pledge->amount_paid, 2) }}</p>
                <p class="text-xs text-gray-500">remaining</p>
            </div>
        </div>
        @empty
        <p class="text-center text-gray-500 py-4">No upcoming pledges</p>
        @endforelse
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('givingChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($monthlyGiving, 'month')) !!},
            datasets: [
                {
                    label: 'Tithes',
                    data: {!! json_encode(array_column($monthlyGiving, 'tithes')) !!},
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                },
                {
                    label: 'Offerings',
                    data: {!! json_encode(array_column($monthlyGiving, 'offerings')) !!},
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                x: { stacked: true },
                y: { 
                    stacked: true,
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '{{ $currencySymbol }}' + value;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
