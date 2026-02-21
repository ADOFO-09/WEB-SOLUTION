@extends('layouts.admin')

@section('title', 'Create User Account for Member')

@section('header')
<div class="flex items-center">
    <a href="{{ route('admin.members.show', $member) }}" class="mr-3 text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
    </a>
    <h1 class="text-2xl font-bold text-gray-900">Create User Account</h1>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Member Info -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Member Profile</h3>
        <div class="flex items-center">
            @if($member->photo_path)
            <img src="{{ asset('storage/' . $member->photo_path) }}" class="w-16 h-16 rounded-full object-cover">
            @else
            <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center">
                <span class="text-xl font-bold text-indigo-600">{{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}</span>
            </div>
            @endif
            <div class="ml-4">
                <h4 class="text-lg font-medium text-gray-900">{{ $member->full_name }}</h4>
                <p class="text-gray-500">{{ $member->member_id }}</p>
                <p class="text-sm text-gray-500">{{ $member->phone_primary }} • {{ $member->email ?? 'No email' }}</p>
            </div>
        </div>
    </div>

    <!-- Create Account Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Account Details</h3>
        
        <form action="{{ route('admin.members.store-user', $member) }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" value="{{ $member->full_name }}" disabled
                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm">
                    <p class="mt-1 text-xs text-gray-500">Name is taken from member profile.</p>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $member->email) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <input type="password" name="password" id="password" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700">Role (Optional)</label>
                    <select name="role_id" id="role_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">No admin role (Member Portal only)</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Leave empty for regular members who only need Member Portal access.</p>
                </div>
            </div>

            <div class="bg-green-50 rounded-lg p-4 mt-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-green-800">
                        <p class="font-medium">This account will automatically be linked to this member.</p>
                        <p class="text-green-700">The member will be able to log in and access the Member Portal immediately.</p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('admin.members.show', $member) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
