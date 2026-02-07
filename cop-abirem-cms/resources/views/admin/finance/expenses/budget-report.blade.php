@extends('layouts.admin')

@section('title', 'Budget Report')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.expenses.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Budget Report {{ $year }}</h1>
        </div>
        <form action="" method="GET" class="flex items-center space-x-2">
            <select name="year" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>
@endsection

@section('content')
    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">Total Budget</div>
            <div class="text-3xl font-bold text-gray-900">GH₵ {{ number_format($totalBudget, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">Total Spent</div>
            <div class="text-3xl font-bold text-red-600">GH₵ {{ number_format($totalSpent, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">Remaining</div>
            <div class="text-3xl font-bold {{ ($totalBudget - $totalSpent) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                GH₵ {{ number_format($totalBudget - $totalSpent, 2) }}
            </div>
        </div>
    </div>

    <!-- Overall Progress -->
    @if($totalBudget > 0)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Overall Budget Utilization</span>
            <span class="text-sm font-medium text-gray-700">{{ round(($totalSpent / $totalBudget) * 100, 1) }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-4">
            @php $overallPct = min(100, round(($totalSpent / $totalBudget) * 100, 1)); @endphp
            <div class="h-4 rounded-full {{ $overallPct > 90 ? 'bg-red-500' : ($overallPct > 70 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                 style="width: {{ $overallPct }}%"></div>
        </div>
    </div>
    @endif

    <!-- Category Breakdown -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Category Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Spent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remaining</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transactions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $category->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">GH₵ {{ number_format($category->budget_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-red-600">GH₵ {{ number_format($category->spent, 2) }}</td>
                        <td class="px-6 py-4 text-sm {{ ($category->budget_amount - $category->spent) >= 0 ? 'text-green-600' : 'text-red-600 font-bold' }}">
                            GH₵ {{ number_format($category->budget_amount - $category->spent, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="w-32">
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                        @php $pct = $category->usage_percentage; @endphp
                                        <div class="h-2 rounded-full {{ $pct > 90 ? 'bg-red-500' : ($pct > 70 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                                             style="width: {{ min(100, $pct) }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600">{{ $pct }}%</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $category->paid_expenses_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">Total</td>
                        <td class="px-6 py-4 text-sm text-gray-900">GH₵ {{ number_format($totalBudget, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-red-600">GH₵ {{ number_format($totalSpent, 2) }}</td>
                        <td class="px-6 py-4 text-sm {{ ($totalBudget - $totalSpent) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            GH₵ {{ number_format($totalBudget - $totalSpent, 2) }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
