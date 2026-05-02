@extends('layouts.admin')

@section('title', 'Assign ' . $role->name)

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.staff-positions.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Assign {{ $role->name }}</h1>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Current holders --}}
    @if($currentHolders->isNotEmpty())
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
        <p class="text-sm font-semibold text-amber-800 mb-3">Current holder(s) — will be moved to Member role on reassignment:</p>
        <div class="space-y-2">
            @foreach($currentHolders as $holder)
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-amber-300 flex items-center justify-center text-xs font-bold text-amber-900 flex-shrink-0">
                    {{ strtoupper(substr($holder->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $holder->member?->full_name ?? $holder->name }}</p>
                    <p class="text-xs text-gray-500">{{ $holder->email }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Assignment form --}}
    <div x-data="{
        memberId: '',
        memberMap: {{ $memberUserMap->toJson() }},
        get selected() { return this.memberId ? this.memberMap[this.memberId] : null; },
        get hasUser() { return this.selected?.has_user ?? false; },
        get needsAccount() { return this.memberId !== '' && !this.hasUser; }
    }">
        <form action="{{ route('admin.staff-positions.assign', $role) }}" method="POST"
              class="bg-white shadow rounded-lg">
            @csrf

            <div class="p-6 space-y-5">

                <div>
                    <label for="member_id" class="block text-sm font-medium text-gray-700">
                        Select Member to assign as {{ $role->name }} *
                    </label>
                    <select name="member_id" id="member_id" required
                            x-model="memberId"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">— Choose a member —</option>
                        @foreach($members as $member)
                        <option value="{{ $member->id }}">
                            {{ $member->full_name }}
                            {{ $member->user ? '(has login account)' : '(no login account yet)' }}
                        </option>
                        @endforeach
                    </select>
                    @error('member_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Shown when selected member already has an account --}}
                <div x-show="hasUser" x-cloak
                     class="p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    This member already has a login account (<span x-text="selected?.email" class="font-mono"></span>).
                    Their role will be updated to <strong>{{ $role->name }}</strong>.
                </div>

                {{-- Shown when selected member has no account — need to create one --}}
                <div x-show="needsAccount" x-cloak class="space-y-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm font-semibold text-blue-800">
                        This member has no login account yet. Create one for them:
                    </p>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="their@email.com">
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Temporary Password *</label>
                        <input type="password" name="password" id="password"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Min. 8 characters">
                        @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <p class="text-xs text-blue-600">Share this password with the new staff member. They can change it after logging in.</p>
                </div>

            </div>

            <div class="px-6 py-4 bg-gray-50 flex items-center justify-between rounded-b-lg">
                <a href="{{ route('admin.staff-positions.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" x-bind:disabled="!memberId"
                        class="px-5 py-2 border border-transparent rounded-md text-sm font-semibold text-white disabled:opacity-50 disabled:cursor-not-allowed"
                        style="background-color:#1e3a5f;">
                    Confirm Assignment
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
