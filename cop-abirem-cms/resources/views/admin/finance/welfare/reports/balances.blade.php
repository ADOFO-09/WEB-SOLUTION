@extends('layouts.admin')

@section('title', 'Welfare Balances')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Welfare Balances</h1>
    </div>
@endsection

@section('content')
    <!-- Filter by status -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <form action="{{ route('admin.welfare.reports.balances') }}" method="GET" class="flex gap-3 flex-wrap">
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" name="status" value="all" {{ $status === 'all' ? 'checked' : '' }} class="text-indigo-600" onchange="this.form.submit()">
                    All Members
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" name="status" value="arrears" {{ $status === 'arrears' ? 'checked' : '' }} class="text-red-600" onchange="this.form.submit()">
                    <span class="text-red-600 font-medium">In Arrears</span>
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" name="status" value="paid" {{ $status === 'paid' ? 'checked' : '' }} class="text-green-600" onchange="this.form.submit()">
                    <span class="text-green-600 font-medium">Fully Paid</span>
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" name="status" value="credit" {{ $status === 'credit' ? 'checked' : '' }} class="text-blue-600" onchange="this.form.submit()">
                    <span class="text-blue-600 font-medium">In Credit</span>
                </label>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Expected Total</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Paid Total</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($balances as $row)
                    @php
                        $balance = $row['balance'] ?? 0;
                        $isPaid    = $balance == 0;
                        $isArrears = $balance > 0;
                        $isCredit  = $balance < 0;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $row['member']->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $row['member']->member_id }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $row['member']->welfare_start_date?->format('M d, Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700">
                            {{ $currencySymbol }} {{ number_format($row['expected'] ?? 0, 2) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-green-600 font-medium">
                            {{ $currencySymbol }} {{ number_format($row['paid'] ?? 0, 2) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-bold {{ $isArrears ? 'text-red-600' : ($isCredit ? 'text-blue-600' : 'text-green-600') }}">
                            {{ $isCredit ? '-' : '' }}{{ $currencySymbol }} {{ number_format(abs($balance), 2) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($isArrears)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Arrears</span>
                            @elseif($isCredit)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Credit</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">No enrolled members found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
