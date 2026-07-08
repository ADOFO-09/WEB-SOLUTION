@extends('layouts.admin')

@php $editing = isset($group); @endphp

@section('title', $editing ? 'Edit Group — ' . $group->name : 'New Group — ' . $ministry->name)

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.ministries.groups.index', $ministry) }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $editing ? 'Edit Group' : 'New Sub-Group' }}
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $ministry->name }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-lg shadow p-6">

        @if($errors->any())
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 p-4">
            <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST"
              data-track-changes="true"
              action="{{ $editing
                  ? route('admin.ministries.groups.update', [$ministry, $group])
                  : route('admin.ministries.groups.store', $ministry) }}">
            @csrf
            @if($editing) @method('PUT') @endif

            {{-- Name --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Group Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name"
                       value="{{ old('name', $editing ? $group->name : '') }}"
                       required maxlength="100"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 @enderror"
                       placeholder="e.g. Esther, Abigail, Choir A…">
                @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" maxlength="500"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                          placeholder="Optional description for this group…">{{ old('description', $editing ? $group->description : '') }}</textarea>
            </div>

            {{-- Group Leader (must be a current ministry member) --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Group Leader <span class="text-gray-400 font-normal">(optional)</span></label>
                <select name="leader_member_id"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('leader_member_id') border-red-300 @enderror">
                    <option value="">— No leader assigned —</option>
                    @foreach($ministryMembers as $m)
                    <option value="{{ $m->id }}"
                        {{ old('leader_member_id', $editing ? $group->leader_member_id : '') == $m->id ? 'selected' : '' }}>
                        {{ $m->full_name }}{{ $m->phone_primary ? ' (' . $m->phone_primary . ')' : '' }}
                    </option>
                    @endforeach
                </select>
                @error('leader_member_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-400">Only active members of {{ $ministry->name }} can be assigned as group leader.</p>
            </div>

            {{-- Active --}}
            <div class="mb-6 flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                       {{ old('is_active', $editing ? ($group->is_active ? '1' : '0') : '1') == '1' ? 'checked' : '' }}>
                <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.ministries.groups.index', $ministry) }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
                    {{ $editing ? 'Save Changes' : 'Create Group' }}
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
