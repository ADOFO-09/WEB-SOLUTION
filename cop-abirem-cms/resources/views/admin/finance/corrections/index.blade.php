@extends('layouts.admin')

@section('title', 'Ledger Corrections')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Ledger Corrections</h1>
        <p class="text-sm text-gray-500 mt-1">Void entries, restore records, and view the full audit trail.</p>
    </div>
    <form method="GET" class="mt-4 sm:mt-0 flex items-center gap-2">
        <select name="type" onchange="this.form.submit()"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <option value="all"      {{ $typeFilter === 'all'      ? 'selected' : '' }}>All Types</option>
            <option value="tithe"    {{ $typeFilter === 'tithe'    ? 'selected' : '' }}>Tithes</option>
            <option value="offering" {{ $typeFilter === 'offering' ? 'selected' : '' }}>Offerings</option>
            <option value="donation" {{ $typeFilter === 'donation' ? 'selected' : '' }}>Donations</option>
            <option value="expense"  {{ $typeFilter === 'expense'  ? 'selected' : '' }}>Expenses</option>
        </select>
    </form>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Summary stat cards --}}
    @php
        $totalVoided   = $voidedTithes->count() + $voidedOfferings->count() + $voidedDonations->count() + $voidedExpenses->count();
        $totalAdjusted = $adjustedTithes->count() + $adjustedOfferings->count() + $adjustedDonations->count() + $adjustedExpenses->count();
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-400">
            <div class="text-sm text-gray-500">Voided Entries</div>
            <div class="text-2xl font-bold text-red-600">{{ $totalVoided }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-400">
            <div class="text-sm text-gray-500">Adjusted Entries</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $totalAdjusted }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-400">
            <div class="text-sm text-gray-500">Audit Events (30d)</div>
            <div class="text-2xl font-bold text-blue-600">{{ $auditLogs->total() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-400">
            <div class="text-sm text-gray-500">Filter</div>
            <div class="text-sm font-semibold text-gray-700 mt-1 capitalize">{{ $typeFilter === 'all' ? 'All Types' : $typeFilter . 's' }}</div>
        </div>
    </div>

    {{-- Recent Audit Activity --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">Recent Audit Activity <span class="text-xs text-gray-500 font-normal">(last 30 days)</span></h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date/Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entry Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entry ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Performed By</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">History</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($auditLogs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-500">
                            {{ $log->created_at->format('M d, Y') }}<br>
                            <span class="text-xs">{{ $log->created_at->format('H:i') }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $log->entry_type_color }}">
                                {{ ucfirst($log->entry_type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('admin.finance.corrections.history', [$log->entry_type, $log->entry_id]) }}"
                               class="text-indigo-600 hover:text-indigo-900 font-mono text-xs">#{{ $log->entry_id }}</a>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $log->action_color }}">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs">
                            {{ $log->reason ? Str::limit($log->reason, 60) : '—' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-900">
                            {{ $log->performer?->name ?? 'System' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.finance.corrections.history', [$log->entry_type, $log->entry_id]) }}"
                               class="text-xs text-indigo-600 hover:underline">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400">No audit activity in the last 30 days.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($auditLogs->hasPages())
        <div class="px-4 py-3 border-t">{{ $auditLogs->appends(request()->query())->links() }}</div>
        @endif
    </div>

    {{-- Voided Entries --}}
    @php
        $allVoided = collect()
            ->merge($voidedTithes->map(fn($e) => ['type' => 'tithe',    'entry' => $e, 'ref' => $e->receipt_number ?? $e->reference_number,  'amount' => $e->amount]))
            ->merge($voidedOfferings->map(fn($e) => ['type' => 'offering', 'entry' => $e, 'ref' => $e->reference_number, 'amount' => $e->amount]))
            ->merge($voidedDonations->map(fn($e) => ['type' => 'donation', 'entry' => $e, 'ref' => $e->reference_number, 'amount' => $e->amount]))
            ->merge($voidedExpenses->map(fn($e) => ['type' => 'expense',  'entry' => $e, 'ref' => $e->reference_number,  'amount' => $e->amount]))
            ->sortByDesc(fn($r) => $r['entry']->voided_at);
        if ($typeFilter !== 'all') {
            $allVoided = $allVoided->filter(fn($r) => $r['type'] === $typeFilter);
        }
    @endphp

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-red-200 bg-red-50 flex items-center justify-between">
            <h2 class="font-semibold text-red-800">
                <svg class="w-4 h-4 inline mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                Voided Entries ({{ $allVoided->count() }})
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-red-100 text-sm">
                <thead class="bg-red-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-red-700 uppercase">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-red-700 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-red-700 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-red-700 uppercase">Void Reason</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-red-700 uppercase">Voided By / Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-red-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-red-50">
                    @forelse($allVoided as $row)
                    @php $e = $row['entry']; @endphp
                    <tr class="bg-red-50 hover:bg-red-100">
                        <td class="px-4 py-3 font-mono font-medium text-red-900">{{ $row['ref'] }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ ucfirst($row['type']) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-mono text-red-800">
                            <span class="line-through">{{ $currencySymbol }} {{ number_format($row['amount'], 2) }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700 max-w-xs">{{ Str::limit($e->void_reason, 60) }}</td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $e->voidedByUser?->name ?? 'Unknown' }}<br>
                            <span class="text-xs">{{ $e->voided_at?->format('M d, Y H:i') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.finance.corrections.history', [$row['type'], $e->id]) }}"
                                   class="text-xs text-indigo-600 hover:underline">History</a>
                                @can('corrections.restore')
                                <form method="POST"
                                      action="{{ route('admin.finance.corrections.restore', [$row['type'], $e->id]) }}"
                                      onsubmit="return confirm('Restore this entry to active?')">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded text-green-700 bg-green-50 border border-green-200 hover:bg-green-100">
                                        Restore
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No voided entries.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Adjusted Entries --}}
    @php
        $allAdjusted = collect()
            ->merge($adjustedTithes->map(fn($e) => ['type' => 'tithe',    'entry' => $e, 'ref' => $e->receipt_number ?? $e->reference_number, 'amount' => $e->amount]))
            ->merge($adjustedOfferings->map(fn($e) => ['type' => 'offering', 'entry' => $e, 'ref' => $e->reference_number, 'amount' => $e->amount]))
            ->merge($adjustedDonations->map(fn($e) => ['type' => 'donation', 'entry' => $e, 'ref' => $e->reference_number, 'amount' => $e->amount]))
            ->merge($adjustedExpenses->map(fn($e) => ['type' => 'expense',  'entry' => $e, 'ref' => $e->reference_number, 'amount' => $e->amount]))
            ->sortByDesc(fn($r) => $r['entry']->updated_at);
        if ($typeFilter !== 'all') {
            $allAdjusted = $allAdjusted->filter(fn($r) => $r['type'] === $typeFilter);
        }
    @endphp

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-yellow-200 bg-yellow-50 flex items-center justify-between">
            <h2 class="font-semibold text-yellow-800">
                <svg class="w-4 h-4 inline mr-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Adjusted Entries ({{ $allAdjusted->count() }})
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-yellow-100 text-sm">
                <thead class="bg-yellow-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-yellow-700 uppercase">Original Ref</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-yellow-700 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-yellow-700 uppercase">Original Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-yellow-700 uppercase">Adjustment Ref</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-yellow-700 uppercase">New Amount</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-yellow-700 uppercase">History</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-yellow-50">
                    @forelse($allAdjusted as $row)
                    @php $e = $row['entry']; $adj = $e->adjustmentEntry; @endphp
                    <tr class="bg-yellow-50 hover:bg-yellow-100">
                        <td class="px-4 py-3 font-mono font-medium text-yellow-900">{{ $row['ref'] }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ ucfirst($row['type']) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-mono text-yellow-800 line-through">{{ $currencySymbol }} {{ number_format($row['amount'], 2) }}</td>
                        <td class="px-4 py-3 font-mono text-blue-700">
                            {{ $adj?->reference_number ?? '—' }}
                        </td>
                        <td class="px-4 py-3 font-mono text-green-700 font-semibold">
                            {{ $adj ? $currencySymbol . ' ' . number_format($adj->amount, 2) : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.finance.corrections.history', [$row['type'], $e->id]) }}"
                               class="text-xs text-indigo-600 hover:underline">History</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No adjusted entries.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
