@extends('layouts.admin')

@section('title', 'Set Your Password')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="w-full max-w-md">

        {{-- Warning banner --}}
        <div class="mb-6 p-4 bg-amber-50 border border-amber-300 rounded-lg flex items-start space-x-3">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-800">Password change required</p>
                <p class="text-sm text-amber-700 mt-0.5">
                    Your account was set up with a temporary password. Please choose a new one before continuing.
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-8">
            <div class="mb-6 text-center">
                <div class="w-14 h-14 rounded-full mx-auto flex items-center justify-center mb-3"
                     style="background-color:#1e3a5f;">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Set Your Password</h2>
                <p class="text-sm text-gray-500 mt-1">Logged in as <strong>{{ auth()->user()->name }}</strong></p>
            </div>

            @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <ul class="text-sm text-red-600 space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.password.change.submit') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" name="password" id="password" required autofocus
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="Minimum 8 characters">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="Repeat your new password">
                </div>

                <button type="submit"
                        class="w-full py-2.5 px-4 border border-transparent rounded-md text-sm font-semibold text-white"
                        style="background-color:#1e3a5f;">
                    Set Password & Continue
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
