@extends('layouts.admin')

@section('title', 'My Profile')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
        <a href="{{ route('admin.profile.edit') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            Edit Profile
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-8 text-center bg-gradient-to-r from-indigo-500 to-purple-600">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-white text-indigo-600 text-3xl font-bold">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <h2 class="mt-4 text-xl font-bold text-white">{{ $user->name }}</h2>
            <p class="text-indigo-100">{{ $user->role->name ?? 'User' }}</p>
        </div>

        <div class="p-6">
            <dl class="divide-y divide-gray-200">
                <div class="py-4 flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="text-sm text-gray-900">{{ $user->email }}</dd>
                </div>
                <div class="py-4 flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="text-sm text-gray-900">{{ $user->phone ?? 'Not set' }}</dd>
                </div>
                <div class="py-4 flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                    <dd class="text-sm text-gray-900">{{ $user->role->name ?? 'N/A' }}</dd>
                </div>
                <div class="py-4 flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                    <dd class="text-sm text-gray-900">{{ $user->created_at->format('F d, Y') }}</dd>
                </div>
                <div class="py-4 flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                    <dd class="text-sm text-gray-900">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</dd>
                </div>
            </dl>

            <div class="mt-6 pt-6 border-t">
                <a href="{{ route('admin.profile.password') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    Change Password →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
