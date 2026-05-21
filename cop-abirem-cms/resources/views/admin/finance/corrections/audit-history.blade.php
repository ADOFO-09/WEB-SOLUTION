@extends('layouts.admin')

@section('title', 'Audit History')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Audit History</h1>
        <p class="text-sm text-gray-500 mt-1">
            {{ ucfirst($type) }} #{{ $id }}
            @if($entry)
                &mdash; {{ $entry->reference_number ?? ($entry->receipt_number ?? '') }}
            @endif
        </p>
    </div>
    <a href="{{ route('admin.finance.corrections.index') }}"
       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
        &larr; Back to Corrections
    </a>
</div>
@endsection

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Entry Status Card --}}
    @if($entry)
    <div class="bg-white rounded-lg shadow p-6 flex items-start gap-6">
        <div class="flex-1 grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
            <div>
                <div class="text-xs text-gray-500 uppercase font-medium">Reference</div>
                <div class="font-mono font-semibold text-gray-900">{{ $entry->reference_number ?? $entry->receipt_number }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase font-medium">Amount</div>
                <div class="font-semibold text-gray-900">{{ $currencySymbol }} {{ number_format($entry->amount, 2) }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase font-medium">Ledger Status</div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $entry->ledger_status_badge }}">
                    {{ ucfirst($entry->ledger_status ?? 'active') }}
                </span>
            </div>
            @if($entry->isVoided())
            <div class="col-span-2 md:col-span-3">
                <div class="text-xs text-gray-500 uppercase font-medium">Void Reason</div>
                <div class="text-red-700 italic">{{ $entry->void_reason }}</div>
            </div>
            @endif
            @if($entry->isAdjusted() && $entry->adjustmentEntry)
            <div class="col-span-2 md:col-span-3">
                <div class="text-xs text-gray-500 uppercase font-medium">Adjustment Entry</div>
                <span class="font-mono text-blue-700">{{ $entry->adjustmentEntry->reference_number }}</span>
                &mdash; {{ $currencySymbol }} {{ number_format($entry->adjustmentEntry->amount, 2) }}
            </div>
            @endif
            @if($entry->isAdjustment() && $entry->originalEntry)
            <div class="col-span-2 md:col-span-3">
                <div class="text-xs text-gray-500 uppercase font-medium">Adjusts Original Entry</div>
                <span class="font-mono text-yellow-700">{{ $entry->originalEntry->reference_number }}</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Timeline --}}
    @php $displayLogs = $logs->isNotEmpty() ? $logs : ($auditLogs ?? collect()); @endphp
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="font-semibold text-gray-900">Change Timeline ({{ $displayLogs->count() }} events)</h2>
        </div>

        @forelse($displayLogs as $log)
        <div class="border-b border-gray-100 px-6 py-4 last:border-0 hover:bg-gray-50">
            <div class="flex items-start gap-4">
                <div class="mt-0.5 flex-shrink-0">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $log->action_color }}">
                        {{ ucfirst($log->action) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-sm font-medium text-gray-900">
                            {{ $log->performer?->name ?? 'System' }}
                        </span>
                        <span class="text-xs text-gray-400 whitespace-nowrap">
                            {{ $log->created_at->format('M d, Y H:i:s') }}
                        </span>
                    </div>

                    @if($log->reason)
                    <p class="text-sm text-gray-600 mt-1 italic">"{{ $log->reason }}"</p>
                    @endif

                    @if($log->old_values && $log->new_values && $log->action === 'updated')
                    @php
                        $skip = ['updated_at', 'ledger_status', 'voided_by', 'voided_at', 'void_reason', 'adjusted_by_id'];
                        $changes = collect($log->new_values)->filter(function($val, $key) use ($log, $skip) {
                            return !in_array($key, $skip)
                                && isset($log->old_values[$key])
                                && $log->old_values[$key] != $val;
                        });
                    @endphp
                    @if($changes->isNotEmpty())
                    <div class="mt-2 space-y-1">
                        @foreach($changes as $field => $newVal)
                        <div class="flex items-center gap-2 text-xs">
                            <span class="font-medium text-gray-500 capitalize">{{ str_replace('_', ' ', $field) }}</span>
                            <span class="text-red-500 line-through">{{ $log->old_values[$field] }}</span>
                            <span class="text-gray-400">&rarr;</span>
                            <span class="text-green-600">{{ $newVal }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    @endif

                    @php
                        $oldVals = is_array($log->old_values) ? $log->old_values : (is_string($log->old_values) ? json_decode($log->old_values, true) : []);
                        $newVals = is_array($log->new_values) ? $log->new_values : (is_string($log->new_values) ? json_decode($log->new_values, true) : []);
                    @endphp
                    @if(is_array($oldVals) && is_array($newVals) && count($newVals) && in_array($log->action, ['voided', 'adjusted', 'restored']))
                    <div class="mt-2 space-y-1">
                        @foreach($newVals as $field => $newVal)
                        @if(isset($oldVals[$field]) && $oldVals[$field] != $newVal)
                        <div class="flex items-center gap-2 text-xs">
                            <span class="font-medium text-gray-500 capitalize">{{ str_replace('_', ' ', $field) }}</span>
                            <span class="text-red-500 line-through">{{ $oldVals[$field] }}</span>
                            <span class="text-gray-400">&rarr;</span>
                            <span class="text-green-600">{{ $newVal }}</span>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @endif

                    @if($log->ip_address)
                    <p class="text-xs text-gray-400 mt-1">IP: {{ $log->ip_address }}</p>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="px-6 py-12 text-center text-gray-400">No audit events recorded for this entry.</div>
        @endforelse
    </div>

</div>
@endsection
