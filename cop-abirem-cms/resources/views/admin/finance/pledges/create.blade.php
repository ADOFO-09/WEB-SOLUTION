@extends('layouts.admin')

@section('title', isset($pledge) ? 'Edit Pledge' : 'Create Pledge')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.pledges.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ isset($pledge) ? 'Edit Pledge' : 'Create Pledge' }}</h1>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ isset($pledge) ? route('admin.pledges.update', $pledge) : route('admin.pledges.store') }}" 
          method="POST" class="space-y-6">
        @csrf
        @if(isset($pledge))
            @method('PUT')
        @endif

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Pledge Details</h3>
            </div>
            <div class="p-6 space-y-4">
                <!-- Member -->
                <div>
                    <label for="member_id" class="block text-sm font-medium text-gray-700">Member *</label>
                    <select name="member_id" id="member_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select a member</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" 
                                {{ old('member_id', $pledge->member_id ?? $selectedMember?->id) == $member->id ? 'selected' : '' }}>
                                {{ $member->full_name }} ({{ $member->member_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Purpose -->
                <div>
                    <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose *</label>
                    <input type="text" name="purpose" id="purpose" 
                           value="{{ old('purpose', $pledge->purpose ?? '') }}" required
                           placeholder="e.g., Building Fund, Missions Support"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('purpose')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Project -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700">Project (Optional)</label>
                    <select name="project_id" id="project_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">No linked project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" 
                                {{ old('project_id', $pledge->project_id ?? $selectedProject?->id) == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Amount -->
                <div>
                    <label for="total_amount" class="block text-sm font-medium text-gray-700">Pledge Amount (GH₵) *</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">GH₵</span>
                        </div>
                        <input type="number" name="total_amount" id="total_amount" step="0.01" min="1"
                               value="{{ old('total_amount', $pledge->total_amount ?? '') }}" required
                               class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    @error('total_amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="pledge_date" class="block text-sm font-medium text-gray-700">Pledge Date *</label>
                        <input type="date" name="pledge_date" id="pledge_date" 
                               value="{{ old('pledge_date', isset($pledge) ? $pledge->pledge_date->format('Y-m-d') : date('Y-m-d')) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('pledge_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                        <input type="date" name="due_date" id="due_date" 
                               value="{{ old('due_date', isset($pledge) && $pledge->due_date ? $pledge->due_date->format('Y-m-d') : '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Payment Frequency -->
                <div>
                    <label for="payment_frequency" class="block text-sm font-medium text-gray-700">Payment Frequency *</label>
                    <select name="payment_frequency" id="payment_frequency" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="one_time" {{ old('payment_frequency', $pledge->payment_frequency ?? '') == 'one_time' ? 'selected' : '' }}>One Time</option>
                        <option value="weekly" {{ old('payment_frequency', $pledge->payment_frequency ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ old('payment_frequency', $pledge->payment_frequency ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ old('payment_frequency', $pledge->payment_frequency ?? '') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                        <option value="annually" {{ old('payment_frequency', $pledge->payment_frequency ?? '') == 'annually' ? 'selected' : '' }}>Annually</option>
                    </select>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $pledge->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.pledges.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                {{ isset($pledge) ? 'Update Pledge' : 'Create Pledge' }}
            </button>
        </div>
    </form>
</div>
@endsection
