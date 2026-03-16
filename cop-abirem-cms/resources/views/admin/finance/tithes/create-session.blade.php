@extends('layouts.admin')

@section('title', 'Record Session Tithe')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Record Session Tithe</h1>
            <p class="text-sm text-gray-500 mt-1">Record total tithe collected during a service</p>
        </div>
        <a href="{{ route('admin.finance.dashboard') }}"
           class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Dashboard
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-2">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-red-800">{{ session('error') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <ul class="text-sm text-red-800 list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.tithes.session.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">

            {{-- Session --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Service / Session <span class="text-red-500">*</span>
                </label>
                <select name="attendance_session_id" required
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">— Select Session —</option>
                    @forelse($sessions as $session)
                    <option value="{{ $session->id }}" {{ old('attendance_session_id') == $session->id ? 'selected' : '' }}>
                        {{ $session->serviceType->name ?? 'Service' }} — {{ $session->service_date->format('D, M d, Y') }}
                        @if($session->status === 'open') (Open) @endif
                    </option>
                    @empty
                    <option value="" disabled>No sessions found</option>
                    @endforelse
                </select>
                @if($sessions->isEmpty())
                <p class="mt-1 text-xs text-yellow-600">
                    No sessions found.
                    <a href="{{ route('admin.attendance.create') }}" class="underline">Create one first.</a>
                </p>
                @endif
            </div>

            {{-- Amount --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Total Tithe Amount (GH₵) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium select-none">GH₵</span>
                    <input type="number" name="amount" step="0.01" min="0.01" required
                           value="{{ old('amount') }}"
                           class="w-full pl-14 text-lg rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="0.00">
                </div>
                <p class="mt-1 text-xs text-gray-500">Enter the total tithe collected during this service — no member breakdown required.</p>
            </div>

            {{-- Payment Method --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Payment Method <span class="text-red-500">*</span>
                </label>
                <select name="payment_method" required
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="mobile_money" {{ old('payment_method') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                    <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="cheque" {{ old('payment_method') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                </select>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                <textarea name="notes" rows="3"
                          class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                          placeholder="Any additional notes about this collection...">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.finance.dashboard') }}"
                   class="px-5 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Record Session Tithe
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
