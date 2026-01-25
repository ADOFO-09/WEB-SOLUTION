@extends('layouts.admin')

@section('title', 'Family Relationships - ' . $member->full_name)

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.members.show', $member) }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Family Relationships</h1>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Current Relationships -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">{{ $member->full_name }}'s Family</h3>
        </div>

        @if($member->familyRelationships->count() > 0)
        <ul class="divide-y divide-gray-200">
            @foreach($member->familyRelationships as $relationship)
            <li class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                        @if($relationship->relatedMember->photo_path)
                            <img class="h-10 w-10 rounded-full object-cover" 
                                 src="{{ asset('storage/' . $relationship->relatedMember->photo_path) }}" 
                                 alt="">
                        @else
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-indigo-600 font-medium text-sm">
                                    {{ substr($relationship->relatedMember->first_name, 0, 1) }}{{ substr($relationship->relatedMember->last_name, 0, 1) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                            <a href="{{ route('admin.members.show', $relationship->relatedMember) }}" class="hover:text-indigo-600">
                                {{ $relationship->relatedMember->full_name }}
                            </a>
                        </div>
                        <div class="text-sm text-gray-500">{{ $relationship->relationship_label }}</div>
                    </div>
                </div>
                
                @can('members.edit')
                <form action="{{ route('admin.members.family', $member) }}" method="POST" 
                      onsubmit="return confirm('Remove this family relationship?');">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="related_member_id" value="{{ $relationship->relatedMember->id }}">
                    <button type="submit" class="text-red-600 hover:text-red-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
                @endcan
            </li>
            @endforeach
        </ul>
        @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No family relationships</h3>
            <p class="mt-1 text-sm text-gray-500">Add family members using the form.</p>
        </div>
        @endif
    </div>

    <!-- Add Relationship Form -->
    @can('members.edit')
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Add Family Relationship</h3>
        </div>
        <div class="px-6 py-4">
            <form action="{{ route('admin.members.family', $member) }}" method="POST">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="related_member_id" class="block text-sm font-medium text-gray-700">Select Family Member *</label>
                        <select name="related_member_id" id="related_member_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Choose a member...</option>
                            @foreach($availableMembers as $otherMember)
                                @if(!$member->familyRelationships->pluck('related_member_id')->contains($otherMember->id))
                                <option value="{{ $otherMember->id }}">
                                    {{ $otherMember->full_name }} ({{ $otherMember->member_id }})
                                </option>
                                @endif
                            @endforeach
                        </select>
                        @error('related_member_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="relationship_type" class="block text-sm font-medium text-gray-700">Relationship Type *</label>
                        <select name="relationship_type" id="relationship_type" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select relationship...</option>
                            <option value="spouse">Spouse</option>
                            <option value="child">Child</option>
                            <option value="parent">Parent</option>
                            <option value="sibling">Sibling</option>
                        </select>
                        @error('relationship_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    When you add a relationship, the inverse relationship will be automatically created for the other member.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Relationship
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endcan
</div>
@endsection
