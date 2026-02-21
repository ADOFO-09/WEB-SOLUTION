@extends('layouts.member')

@section('title', 'Giving Overview')

@section('header')
<h1 class="text-xl font-bold text-gray-900">Giving Overview</h1>
@endsection

@section('content')
<!-- Year Filter -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <form action="{{ route('member.giving.index') }}" method="GET" class="flex items-center space-x-2">
            <label class="text-sm text-gray-600">Year:</label>
            <select name="year" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach($years as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <a href="{{ route('member.giving.statement', ['year' => $year]) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
        View Statement →
    </a>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-gray-500 mb-1">Total Giving</div>
        <div class="text-2xl font-bold text-gray-900">GH₵ {{ number_format($summary['total'], 2) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-gray-500 mb-1">Tithes</div>
        <div class="text-2xl font-bold text-green-600">GH₵ {{ number_format($summary['tithes'], 2) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-gray-500 mb-1">Offerings</div>
        <div class="text-2xl font-bold text-blue-600">GH₵ {{ number_format($summary['offerings'], 2) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-gray-500 mb-1">Donations</div>
        <div class="text-2xl font-bold text-purple-600">GH₵ {{ number_format($summary['donations'], 2) }}</div>
    </div>
</div>

<!-- Monthly Chart -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Breakdown - {{ $year }}</h3>
    <div class="h-80">
        <canvas id="monthlyChart"></canvas>
    </div>
</div>

<!-- Monthly Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Monthly Details</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tithes</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Offerings</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Donations</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($monthlyData as $data)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $data['month'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-600">GH₵ {{ number_format($data['tithes'], 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-600">GH₵ {{ number_format($data['offerings'], 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-600">GH₵ {{ number_format($data['donations'], 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">GH₵ {{ number_format($data['total'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">Total</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-green-600">GH₵ {{ number_format($summary['tithes'], 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-blue-600">GH₵ {{ number_format($summary['offerings'], 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-purple-600">GH₵ {{ number_format($summary['donations'], 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900">GH₵ {{ number_format($summary['total'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($monthlyData, 'month')) !!},
            datasets: [
                {
                    label: 'Tithes',
                    data: {!! json_encode(array_column($monthlyData, 'tithes')) !!},
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                },
                {
                    label: 'Offerings',
                    data: {!! json_encode(array_column($monthlyData, 'offerings')) !!},
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                },
                {
                    label: 'Donations',
                    data: {!! json_encode(array_column($monthlyData, 'donations')) !!},
                    backgroundColor: 'rgba(147, 51, 234, 0.8)',
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
                            return 'GH₵' + value;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
