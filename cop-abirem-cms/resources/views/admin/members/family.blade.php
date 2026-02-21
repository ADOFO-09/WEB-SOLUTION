@extends('layouts.admin')

@section('title', 'Manage Family - ' . $member->full_name)

@section('header')
<div class="flex items-center justify-between">
    <div class="flex items-center">
        <a href="{{ route('admin.members.show', $member) }}" class="mr-3 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Manage Family</h1>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Member Info -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center mb-4">
                @if($member->photo_path)
                <img src="{{ asset('storage/' . $member->photo_path) }}" class="w-24 h-24 rounded-full mx-auto object-cover">
                @else
                <div class="w-24 h-24 rounded-full bg-indigo-100 mx-auto flex items-center justify-center">
                    <span class="text-2xl font-bold text-indigo-600">{{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}</span>
                </div>
                @endif
                <h3 class="mt-3 text-lg font-medium text-gray-900">{{ $member->full_name }}</h3>
                <p class="text-sm text-gray-500">{{ $member->member_id }}</p>
            </div>
            
            <!-- Add Relationship Form -->
            <form action="{{ route('admin.members.family.store', $member) }}" method="POST" class="mt-6 border-t pt-6">
                @csrf
                <h4 class="font-medium text-gray-900 mb-4">Add Family Member</h4>
                
                <div class="space-y-4">
                    <div>
                        <label for="related_member_id" class="block text-sm font-medium text-gray-700">Select Member</label>
                        <select name="related_member_id" id="related_member_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Choose a member...</option>
                            @foreach($availableMembers as $m)
                            <option value="{{ $m->id }}">{{ $m->full_name }} ({{ $m->member_id }})</option>
                            @endforeach
                        </select>
                        @error('related_member_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="relationship_type" class="block text-sm font-medium text-gray-700">Relationship</label>
                        <select name="relationship_type" id="relationship_type" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select relationship...</option>
                            @foreach($relationshipTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('relationship_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                        Add Relationship
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Family Members List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Family Members</h3>
            </div>
            
            @if($member->familyRelationships->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($member->familyRelationships as $relationship)
                <div class="p-6 flex items-center justify-between">
                    <div class="flex items-center">
                        @if($relationship->relatedMember->photo_path)
                        <img src="{{ asset('storage/' . $relationship->relatedMember->photo_path) }}" 
                             class="w-12 h-12 rounded-full object-cover">
                        @else
                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-600">
                                {{ substr($relationship->relatedMember->first_name, 0, 1) }}{{ substr($relationship->relatedMember->last_name, 0, 1) }}
                            </span>
                        </div>
                        @endif
                        <div class="ml-4">
                            <a href="{{ route('admin.members.show', $relationship->relatedMember) }}" 
                               class="text-gray-900 font-medium hover:text-indigo-600">
                                {{ $relationship->relatedMember->full_name }}
                            </a>
                            <p class="text-sm text-gray-500">{{ $relationship->relatedMember->member_id }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                            {{ ucfirst($relationship->relationship_type) }}
                        </span>
                        
                        <form action="{{ route('admin.members.family.destroy', [$member, $relationship]) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to remove this relationship?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-gray-500">No family relationships added yet.</p>
                <p class="text-sm text-gray-400 mt-1">Use the form on the left to add family members.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
