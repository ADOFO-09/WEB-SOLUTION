@extends('layouts.admin')

@section('title', 'Manage Members - ' . $ministry->name)

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.ministries.show', $ministry) }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Manage Members - {{ $ministry->name }}</h1>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Current Members -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Current Members ({{ $ministry->activeMembers->count() }})</h3>
        </div>

        @if($ministry->activeMembers->count() > 0)
        <ul class="divide-y divide-gray-200 max-h-[600px] overflow-y-auto">
            @foreach($ministry->activeMembers as $member)
            <li class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            @if($member->photo_path)
                                <img class="h-10 w-10 rounded-full object-cover" 
                                     src="{{ asset('storage/' . $member->photo_path) }}" alt="">
                            @else
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-600 font-medium text-sm">
                                        {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $member->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $member->phone_primary }}</div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <!-- Role Badge -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $member->pivot->role == 'leader' ? 'bg-yellow-100 text-yellow-800' : 
                               ($member->pivot->role == 'assistant_leader' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst(str_replace('_', ' ', $member->pivot->role)) }}
                        </span>

                        <!-- Update Role Form -->
                        @can('ministries.edit')
                        <form action="{{ route('admin.ministries.members', $ministry) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="member_id" value="{{ $member->id }}">
                            <select name="role" onchange="this.form.submit()" 
                                    class="text-xs border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="member" {{ $member->pivot->role == 'member' ? 'selected' : '' }}>Member</option>
                                <option value="assistant_leader" {{ $member->pivot->role == 'assistant_leader' ? 'selected' : '' }}>Asst. Leader</option>
                                <option value="leader" {{ $member->pivot->role == 'leader' ? 'selected' : '' }}>Leader</option>
                            </select>
                        </form>

                        <!-- Remove Button -->
                        <form action="{{ route('admin.ministries.members', $ministry) }}" method="POST" 
                              onsubmit="return confirm('Remove this member from the ministry?');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="member_id" value="{{ $member->id }}">
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
        @else
        <div class="px-6 py-12 text-center">
            <p class="text-gray-500">No members in this ministry yet.</p>
        </div>
        @endif
    </div>

    <!-- Add Members -->
    @can('ministries.edit')
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Add Member</h3>
        </div>

        <div class="p-6">
            <form action="{{ route('admin.ministries.members', $ministry) }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="member_id" class="block text-sm font-medium text-gray-700">Select Member *</label>
                    <select name="member_id" id="member_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Choose a member...</option>
                        @foreach($availableMembers as $member)
                            <option value="{{ $member->id }}">
                                {{ $member->full_name }} ({{ $member->member_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role *</label>
                    <select name="role" id="role" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="member">Member</option>
                        <option value="assistant_leader">Assistant Leader</option>
                        <option value="leader">Leader</option>
                    </select>
                    @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit" 
                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add to Ministry
                </button>
            </form>
        </div>

        @if($availableMembers->isEmpty())
        <div class="px-6 pb-6">
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            All active members are already in this ministry.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endcan
</div>
@endsection
