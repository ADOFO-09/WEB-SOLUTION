@extends('layouts.admin')

@section('title', 'Change Password')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.profile.show') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Change Password</h1>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('admin.profile.password.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Update Password</h3>
                <p class="mt-1 text-sm text-gray-500">Ensure your account is using a secure password.</p>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                    <input type="password" name="current_password" id="current_password" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('current_password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" name="password" id="password" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.profile.show') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">Update Password</button>
        </div>
    </form>
</div>
@endsection
