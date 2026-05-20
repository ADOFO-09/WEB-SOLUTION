@extends('layouts.admin')

@section('title', isset($visitor) ? 'Edit Visitor' : 'Register Visitor')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.visitors.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ isset($visitor) ? 'Edit Visitor' : 'Register Visitor' }}</h1>
    </div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <form action="{{ isset($visitor) ? route('admin.visitors.update', $visitor) : route('admin.visitors.store') }}" 
          method="POST" class="space-y-6">
        @csrf
        @if(isset($visitor))
            @method('PUT')
        @endif

        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                    <input type="text" name="first_name" id="first_name" 
                           value="{{ old('first_name', $visitor->first_name ?? '') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('first_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                    <input type="text" name="last_name" id="last_name" 
                           value="{{ old('last_name', $visitor->last_name ?? '') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('last_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number *</label>
                    <input type="tel" name="phone" id="phone" 
                           value="{{ old('phone', $visitor->phone ?? '') }}" required
                           placeholder="0244123456"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" 
                           value="{{ old('email', $visitor->email ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <input type="text" name="address" id="address" 
                           value="{{ old('address', $visitor->address ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Visit Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Visit Information</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="first_visit_date" class="block text-sm font-medium text-gray-700">First Visit Date *</label>
                    <input type="date" name="first_visit_date" id="first_visit_date" 
                           value="{{ old('first_visit_date', isset($visitor) ? $visitor->first_visit_date->format('Y-m-d') : date('Y-m-d')) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('first_visit_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="referral_source" class="block text-sm font-medium text-gray-700">How did they hear about us? *</label>
                    <select name="referral_source" id="referral_source" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            onchange="toggleReferredBy(this.value)">
                        <option value="">Select source</option>
                        <option value="member" {{ old('referral_source', $visitor->referral_source ?? '') == 'member' ? 'selected' : '' }}>Member Referral</option>
                        <option value="walk_in" {{ old('referral_source', $visitor->referral_source ?? '') == 'walk_in' ? 'selected' : '' }}>Walk-in</option>
                        <option value="social_media" {{ old('referral_source', $visitor->referral_source ?? '') == 'social_media' ? 'selected' : '' }}>Social Media</option>
                        <option value="event" {{ old('referral_source', $visitor->referral_source ?? '') == 'event' ? 'selected' : '' }}>Event/Program</option>
                        <option value="flyer" {{ old('referral_source', $visitor->referral_source ?? '') == 'flyer' ? 'selected' : '' }}>Flyer/Poster</option>
                        <option value="other" {{ old('referral_source', $visitor->referral_source ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('referral_source')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div id="referred_by_container" class="md:col-span-2" style="{{ old('referral_source', $visitor->referral_source ?? '') != 'member' ? 'display:none' : '' }}">
                    <label for="referred_by_member_id" class="block text-sm font-medium text-gray-700">Referred By (Member)</label>
                    <select name="referred_by_member_id" id="referred_by_member_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select member</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" 
                                {{ old('referred_by_member_id', $visitor->referred_by_member_id ?? '') == $member->id ? 'selected' : '' }}>
                                {{ $member->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(isset($visitor))
                <div>
                    <label for="follow_up_status" class="block text-sm font-medium text-gray-700">Follow-up Status *</label>
                    <select name="follow_up_status" id="follow_up_status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="pending" {{ old('follow_up_status', $visitor->follow_up_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="contacted" {{ old('follow_up_status', $visitor->follow_up_status) == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="interested" {{ old('follow_up_status', $visitor->follow_up_status) == 'interested' ? 'selected' : '' }}>Interested</option>
                        <option value="not_interested" {{ old('follow_up_status', $visitor->follow_up_status) == 'not_interested' ? 'selected' : '' }}>Not Interested</option>
                        <option value="converted" {{ old('follow_up_status', $visitor->follow_up_status) == 'converted' ? 'selected' : '' }}>Converted</option>
                    </select>
                </div>
                @endif
            </div>
        </div>

        <!-- Additional Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Additional Information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label for="prayer_request" class="block text-sm font-medium text-gray-700">Prayer Request</label>
                    <textarea name="prayer_request" id="prayer_request" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('prayer_request', $visitor->prayer_request ?? '') }}</textarea>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $visitor->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.visitors.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                {{ isset($visitor) ? 'Update Visitor' : 'Register Visitor' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleReferredBy(value) {
    const container = document.getElementById('referred_by_container');
    container.style.display = value === 'member' ? 'block' : 'none';
}
</script>
@endpush
@endsection
