@extends('layouts.admin')

@section('title', 'Welfare Benefit')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.welfare.benefits.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Welfare Benefit</h1>
    </div>
@endsection

@section('content')
    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 rounded-md p-4">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="max-w-2xl mx-auto space-y-4">
        <!-- Main Details -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Benefit Details</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.welfare.benefits.edit', $welfareBenefit) }}"
                       class="inline-flex items-center px-3 py-1.5 border border-yellow-300 rounded-md text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.welfare.benefits.destroy', $welfareBenefit) }}"
                          onsubmit="return confirm('Delete this benefit and all its expense lines?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-3 py-1.5 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            <dl class="divide-y divide-gray-200">
                <div class="px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Benefit Date</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $welfareBenefit->benefit_date->format('M d, Y') }}</dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Purpose</dt>
                    <dd class="col-span-2">
                        @php
                            $purposeColors = ['marriage'=>'bg-pink-100 text-pink-800','funeral'=>'bg-gray-100 text-gray-800','childbirth'=>'bg-blue-100 text-blue-800','other'=>'bg-yellow-100 text-yellow-800'];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $purposeColors[$welfareBenefit->purpose] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ \App\Models\WelfareBenefit::PURPOSES[$welfareBenefit->purpose] ?? $welfareBenefit->purpose }}
                        </span>
                    </dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Benefactor</dt>
                    <dd class="text-sm text-gray-900 col-span-2">
                        {{ $welfareBenefit->benefactor_name }}
                        @if($welfareBenefit->member)
                            <span class="text-gray-400 text-xs ml-1">({{ $welfareBenefit->member->full_name }})</span>
                        @endif
                    </dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Main Amount</dt>
                    <dd class="text-sm font-bold text-gray-900 col-span-2">{{ $currencySymbol }} {{ number_format($welfareBenefit->amount, 2) }}</dd>
                </div>
                @if($welfareBenefit->description)
                <div class="px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $welfareBenefit->description }}</dd>
                </div>
                @endif
                <div class="px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Approved By</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $welfareBenefit->approvedBy?->name ?? '—' }}</dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Recorded By</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $welfareBenefit->recordedBy?->name ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Expense Lines -->
        @if($welfareBenefit->welfareBenefitExpenses->isNotEmpty())
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Itemised Expenses</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($welfareBenefit->welfareBenefitExpenses as $expense)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $expense->description }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $currencySymbol }} {{ number_format($expense->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3 bg-gray-50 border-t">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Other Expenses Total:</span>
                    <span class="font-medium">{{ $currencySymbol }} {{ number_format($welfareBenefit->total_other_expenses, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm font-bold text-gray-900 mt-1">
                    <span>Total Cost (Main + Other):</span>
                    <span>{{ $currencySymbol }} {{ number_format($welfareBenefit->total_cost, 2) }}</span>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between text-sm font-bold text-gray-900">
                <span>Total Cost:</span>
                <span>{{ $currencySymbol }} {{ number_format($welfareBenefit->total_cost, 2) }}</span>
            </div>
        </div>
        @endif
    </div>
@endsection
