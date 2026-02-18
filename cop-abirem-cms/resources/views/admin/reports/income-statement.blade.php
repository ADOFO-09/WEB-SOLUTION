@extends('layouts.admin')

@section('title', 'Income Statement')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.reports.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Income Statement</h1>
        </div>
        <form action="" method="GET" class="flex items-center space-x-2">
            <select name="year" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="month" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                <option value="">Full Year</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endfor
            </select>
            <button type="button" onclick="window.print()" class="px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">Print</button>
        </form>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="text-center py-4">
        <h2 class="text-xl font-bold text-gray-900">{{ $periodLabel }}</h2>
        @if($comparison['change'] != 0)
        <p class="text-sm {{ $comparison['change'] > 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ $comparison['change'] > 0 ? '↑' : '↓' }} {{ abs($comparison['change']) }}% vs previous period
        </p>
        @endif
    </div>

    <!-- Income -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 bg-green-50 border-b"><h3 class="text-lg font-semibold text-green-800">Income</h3></div>
        <div class="p-6">
            <table class="w-full">
                <tbody class="divide-y divide-gray-100">
                    <tr><td class="py-3">Tithes</td><td class="py-3 text-right font-medium">GH₵ {{ number_format($income['tithes'], 2) }}</td></tr>
                    <tr><td class="py-3">Offerings</td><td class="py-3 text-right font-medium">GH₵ {{ number_format($income['offerings'], 2) }}</td></tr>
                    <tr><td class="py-3">Donations</td><td class="py-3 text-right font-medium">GH₵ {{ number_format($income['donations'], 2) }}</td></tr>
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-green-200">
                        <td class="py-4 font-bold text-green-800">Total Income</td>
                        <td class="py-4 text-right text-xl font-bold text-green-600">GH₵ {{ number_format($income['total'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Expenses -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 bg-red-50 border-b"><h3 class="text-lg font-semibold text-red-800">Expenses</h3></div>
        <div class="p-6">
            <table class="w-full">
                <tbody class="divide-y divide-gray-100">
                    @forelse($expensesByCategory as $expense)
                    <tr><td class="py-3">{{ $expense->expenseCategory->name ?? 'N/A' }}</td><td class="py-3 text-right font-medium">GH₵ {{ number_format($expense->total, 2) }}</td></tr>
                    @empty
                    <tr><td colspan="2" class="py-4 text-center text-gray-500">No expenses</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-red-200">
                        <td class="py-4 font-bold text-red-800">Total Expenses</td>
                        <td class="py-4 text-right text-xl font-bold text-red-600">GH₵ {{ number_format($totalExpenses, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Net Income -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex justify-between items-center">
            <span class="text-xl font-bold {{ $netIncome >= 0 ? 'text-green-800' : 'text-red-800' }}">Net Income</span>
            <span class="text-3xl font-bold {{ $netIncome >= 0 ? 'text-green-600' : 'text-red-600' }}">GH₵ {{ number_format($netIncome, 2) }}</span>
        </div>
    </div>
</div>
@endsection
