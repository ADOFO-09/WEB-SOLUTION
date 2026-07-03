@extends('layouts.admin')

@section('title', isset($welfareBenefit) ? 'Edit Welfare Benefit' : 'Record Welfare Benefit')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.welfare.benefits.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">
            {{ isset($welfareBenefit) ? 'Edit Welfare Benefit' : 'Record Welfare Benefit' }}
        </h1>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                @if(isset($welfareBenefit))
                <form action="{{ route('admin.welfare.benefits.update', $welfareBenefit) }}" method="POST">
                    @csrf @method('PUT')
                @else
                <form action="{{ route('admin.welfare.benefits.store') }}" method="POST">
                    @csrf
                @endif

                    @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 rounded-md p-4">
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div
                        x-data="{
                            recipientType: '{{ old('recipient_type', isset($welfareBenefit) && $welfareBenefit->member_id ? 'member' : (old('benefactor_name') ? 'other' : 'member')) }}',
                            mainAmount: {{ old('amount', $welfareBenefit->amount ?? 0) }},
                            expenses: {{ json_encode(old('expenses', isset($welfareBenefit) ? $welfareBenefit->welfareBenefitExpenses->map(fn($e) => ['description' => $e->description, 'amount' => $e->amount])->toArray() : [])) }},
                            addExpense() { this.expenses.push({description: '', amount: ''}); },
                            removeExpense(i) { this.expenses.splice(i, 1); },
                            get totalOther() { return this.expenses.reduce((s, e) => s + (parseFloat(e.amount) || 0), 0); },
                            get totalCost() { return (parseFloat(this.mainAmount) || 0) + this.totalOther; }
                        }"
                        class="space-y-5">

                        <!-- Recipient Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Type</label>
                            <div class="flex gap-4 mb-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="recipient_type" value="member" x-model="recipientType" class="text-indigo-600">
                                    <span class="text-sm">Church Member</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="recipient_type" value="other" x-model="recipientType" class="text-indigo-600">
                                    <span class="text-sm">Other (Non-member)</span>
                                </label>
                            </div>

                            <div x-show="recipientType === 'member'">
                                <select name="member_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Select Member --</option>
                                    @foreach($members as $m)
                                        <option value="{{ $m->id }}"
                                            {{ old('member_id', $welfareBenefit->member_id ?? '') == $m->id ? 'selected' : '' }}>
                                            {{ $m->full_name }} ({{ $m->member_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Benefactor Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Recipient / Benefactor Name <span class="text-red-500">*</span>
                                <span class="text-gray-400 font-normal">(required — enter member name or other name)</span>
                            </label>
                            <input type="text" name="benefactor_name"
                                   value="{{ old('benefactor_name', $welfareBenefit->benefactor_name ?? '') }}"
                                   required maxlength="255"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="e.g. John Mensah">
                            @error('benefactor_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Purpose -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Purpose <span class="text-red-500">*</span></label>
                            <select name="purpose" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Select Purpose --</option>
                                @foreach($purposes as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('purpose', $welfareBenefit->purpose ?? '') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('purpose') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Benefit Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Benefit Date <span class="text-red-500">*</span></label>
                            <input type="date" name="benefit_date"
                                   value="{{ old('benefit_date', isset($welfareBenefit) ? $welfareBenefit->benefit_date->format('Y-m-d') : date('Y-m-d')) }}"
                                   required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('benefit_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Main Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Main Amount <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">{{ $currencySymbol }}</span>
                                <input type="number" name="amount" step="0.01" min="0"
                                       x-model="mainAmount"
                                       value="{{ old('amount', $welfareBenefit->amount ?? '') }}"
                                       required
                                       class="pl-12 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="0.00">
                            </div>
                            @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400">(optional)</span></label>
                            <textarea name="description" rows="2"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $welfareBenefit->description ?? '') }}</textarea>
                            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Itemised Other Expenses -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Itemised Other Expenses</label>

                            <template x-for="(expense, i) in expenses" :key="i">
                                <div class="flex gap-2 mb-2">
                                    <input type="text" :name="`expenses[${i}][description]`" x-model="expense.description"
                                           placeholder="Description" required
                                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <input type="number" :name="`expenses[${i}][amount]`" x-model="expense.amount"
                                           step="0.01" min="0.01" placeholder="Amount" required
                                           class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <button type="button" @click="removeExpense(i)"
                                            class="text-red-500 hover:text-red-700 px-2 text-lg font-bold leading-none">×</button>
                                </div>
                            </template>

                            <button type="button" @click="addExpense()"
                                    class="mt-1 inline-flex items-center px-3 py-1.5 border border-dashed border-gray-300 rounded-md text-sm text-gray-600 hover:border-indigo-400 hover:text-indigo-600">
                                + Add Expense Line
                            </button>

                            <!-- Totals -->
                            <div class="mt-3 p-3 bg-gray-50 rounded-md text-sm space-y-1" x-show="expenses.length > 0 || mainAmount > 0">
                                <div class="flex justify-between text-gray-600">
                                    <span>Main Amount:</span>
                                    <span x-text="'{{ $currencySymbol }} ' + (parseFloat(mainAmount) || 0).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between text-gray-600" x-show="expenses.length > 0">
                                    <span>Other Expenses:</span>
                                    <span x-text="'{{ $currencySymbol }} ' + totalOther.toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between font-bold text-gray-900 border-t pt-1">
                                    <span>Total Cost:</span>
                                    <span x-text="'{{ $currencySymbol }} ' + totalCost.toFixed(2)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-2">
                            <a href="{{ route('admin.welfare.benefits.index') }}"
                               class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                {{ isset($welfareBenefit) ? 'Update Benefit' : 'Record Benefit' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
