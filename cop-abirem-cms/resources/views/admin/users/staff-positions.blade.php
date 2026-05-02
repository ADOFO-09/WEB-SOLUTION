@extends('layouts.admin')

@section('title', 'Staff Positions')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.users.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Staff Positions</h1>
                <p class="text-sm text-gray-500 mt-0.5">Assign church office-holders to their roles</p>
            </div>
        </div>
    </div>
@endsection

@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
    {{ session('success') }}
</div>
@endif

<div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
    <strong>How this works:</strong> Each staff role (Elder, Secretary, etc.) should be held by one person at a time.
    Use <strong>Assign New Person</strong> to hand a role to a different member — the previous holder is automatically
    moved back to the regular Member role.
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    @foreach($staffRoles as $role)
    @php
        $roleColors = [
            'elder'          => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'border' => 'border-purple-200', 'icon_bg' => 'bg-purple-500'],
            'secretary'      => ['bg' => 'bg-blue-100',   'text' => 'text-blue-800',   'border' => 'border-blue-200',   'icon_bg' => 'bg-blue-500'],
            'finance'        => ['bg' => 'bg-green-100',  'text' => 'text-green-800',  'border' => 'border-green-200',  'icon_bg' => 'bg-green-600'],
            'ministry_leader'=> ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'border' => 'border-orange-200', 'icon_bg' => 'bg-orange-500'],
        ];
        $colors = $roleColors[$role->slug] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-200', 'icon_bg' => 'bg-gray-500'];
        $activeHolders = $role->users->where('is_active', true);
    @endphp

    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        {{-- Role header --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $colors['bg'] }} {{ $colors['text'] }}">
                    {{ $role->name }}
                </span>
                @if($activeHolders->count() > 1)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">
                    {{ $activeHolders->count() }} holders — needs cleanup
                </span>
                @endif
            </div>
            @can('users.edit')
            <a href="{{ route('admin.staff-positions.assign', $role) }}"
               class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-xs font-semibold text-white"
               style="background-color:#1e3a5f;">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Assign New Person
            </a>
            @endcan
        </div>

        {{-- Current holders --}}
        <div class="p-6">
            @if($activeHolders->isEmpty())
            <div class="text-center py-4">
                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <p class="text-sm text-gray-400">No one assigned to this position</p>
                @can('users.edit')
                <a href="{{ route('admin.staff-positions.assign', $role) }}" class="mt-2 inline-block text-sm text-indigo-600 hover:underline">
                    Assign someone →
                </a>
                @endcan
            </div>
            @else
            <div class="space-y-3">
                @foreach($activeHolders as $holder)
                <div class="flex items-center space-x-3 p-3 rounded-lg {{ $loop->first ? $colors['bg'] : 'bg-gray-50' }}">
                    {{-- Avatar --}}
                    @if($holder->member?->photo_path)
                    <img src="{{ asset('storage/' . $holder->member->photo_path) }}"
                         class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                    @else
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 text-white text-sm font-bold {{ $colors['icon_bg'] }}">
                        {{ strtoupper(substr($holder->name, 0, 2)) }}
                    </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <p class="text-sm font-semibold text-gray-900 truncate">
                                {{ $holder->member?->full_name ?? $holder->name }}
                            </p>
                            @if($loop->first && $activeHolders->count() > 1)
                            <span class="text-xs text-gray-400">(primary)</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 truncate">{{ $holder->email }}</p>
                        @if($holder->member)
                        <p class="text-xs text-gray-400 font-mono">{{ $holder->member->member_id }}</p>
                        @else
                        <p class="text-xs text-amber-600 font-medium">No member profile linked</p>
                        @endif
                    </div>

                    @if($holder->member)
                    <a href="{{ route('admin.members.show', $holder->member) }}"
                       class="text-xs text-indigo-600 hover:underline flex-shrink-0">
                        Profile
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection
