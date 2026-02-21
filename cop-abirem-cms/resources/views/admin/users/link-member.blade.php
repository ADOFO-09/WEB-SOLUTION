@extends('layouts.admin')

@section('title', 'Link User to Member')

@section('header')
<div class="flex items-center">
    <a href="{{ route('admin.users.show', $user) }}" class="mr-3 text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
    </a>
    <h1 class="text-2xl font-bold text-gray-900">Link User to Member</h1>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- User Info -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">User Account</h3>
        <div class="flex items-center">
            <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center">
                <span class="text-xl font-bold text-indigo-600">{{ substr($user->name, 0, 2) }}</span>
            </div>
            <div class="ml-4">
                <h4 class="text-lg font-medium text-gray-900">{{ $user->name }}</h4>
                <p class="text-gray-500">{{ $user->email }}</p>
                @if($user->role)
                <span class="inline-block mt-1 px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                    {{ $user->role->name }}
                </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Link Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Select Member to Link</h3>
        
        @if($members->count() > 0)
        <form action="{{ route('admin.users.link-member', $user) }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label for="member_id" class="block text-sm font-medium text-gray-700 mb-2">Choose a Member</label>
                <select name="member_id" id="member_id" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select a member...</option>
                    @foreach($members as $member)
                    <option value="{{ $member->id }}">
                        {{ $member->full_name }} ({{ $member->member_id }}) - {{ $member->phone_primary }}
                    </option>
                    @endforeach
                </select>
                @error('member_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-gray-500">Only showing active members without existing user accounts.</p>
            </div>

            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium">What happens when you link?</p>
                        <ul class="mt-1 list-disc list-inside text-blue-700">
                            <li>The user will be able to access the Member Portal</li>
                            <li>They can view their giving history, pledges, and attendance</li>
                            <li>They can update their contact information</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.users.show', $user) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Link to Member
                </button>
            </div>
        </form>
        @else
        <div class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-gray-500 mb-2">No available members to link.</p>
            <p class="text-sm text-gray-400">All active members already have user accounts.</p>
        </div>
        @endif
    </div>
</div>
@endsection
