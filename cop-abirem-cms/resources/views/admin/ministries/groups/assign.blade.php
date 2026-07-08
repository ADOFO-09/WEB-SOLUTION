@extends('layouts.admin')

@section('title', 'Assign Members to Groups — ' . $ministry->name)

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.ministries.groups.index', $ministry) }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Assign Members to Groups</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $ministry->name }}</p>
            </div>
        </div>
    </div>
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-sm text-green-800">{{ session('success') }}</p>
</div>
@endif

@if($groups->isEmpty())
<div class="bg-white rounded-lg shadow p-10 text-center">
    <p class="text-sm text-gray-500">No active groups exist for this ministry yet.</p>
    @can('ministry.groups.create')
    <a href="{{ route('admin.ministries.groups.create', $ministry) }}"
       class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
        Create a Group First
    </a>
    @endcan
</div>
@else

{{-- Alpine.js assignment manager --}}
<div x-data="assignmentManager(
    {{ json_encode($currentAssignments) }},
    {{ json_encode($groups->map(fn($g) => ['id' => $g->id, 'name' => $g->name])->values()) }},
    {{ $ministry->activeMembers->count() }}
)">

    {{-- Live group count bar --}}
    <div class="mb-5 grid gap-3" style="grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));">
        <template x-for="group in groups" :key="group.id">
            <div class="rounded-lg border-2 p-3 text-center transition-colors duration-150"
                 :class="{
                     'border-green-400 bg-green-50': isSmallest(group.id),
                     'border-red-300 bg-red-50':    isLargest(group.id),
                     'border-gray-200 bg-white':    !isSmallest(group.id) && !isLargest(group.id)
                 }">
                <div class="text-2xl font-bold"
                     :class="{
                         'text-green-700': isSmallest(group.id),
                         'text-red-600':   isLargest(group.id),
                         'text-indigo-600': !isSmallest(group.id) && !isLargest(group.id)
                     }"
                     x-text="getCount(group.id)"></div>
                <div class="text-xs text-gray-600 mt-0.5 truncate" x-text="group.name"></div>
                <div x-show="isSmallest(group.id)" class="text-xs text-green-600 font-medium mt-0.5">↓ smallest</div>
                <div x-show="isLargest(group.id)"  class="text-xs text-red-500 font-medium mt-0.5">↑ largest</div>
            </div>
        </template>
        {{-- Unassigned counter --}}
        <div class="rounded-lg border-2 border-amber-200 bg-amber-50 p-3 text-center">
            <div class="text-2xl font-bold text-amber-600" x-text="getUnassigned()"></div>
            <div class="text-xs text-gray-600 mt-0.5">Unassigned</div>
        </div>
    </div>

    {{-- Bulk assignment toolbar --}}
    <div x-show="selected.length > 0"
         x-transition
         class="mb-4 bg-indigo-50 border border-indigo-200 rounded-lg p-3 flex flex-wrap items-center gap-3">
        <span class="text-sm font-medium text-indigo-800" x-text="selected.length + ' member(s) selected'"></span>
        <select x-model="bulkGroup"
                class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">— Assign to group… —</option>
            @foreach($groups as $g)
            <option value="{{ $g->id }}">{{ $g->name }}</option>
            @endforeach
            <option value="0">Unassigned</option>
        </select>
        <button type="button" @click="applyBulk()"
                :disabled="!bulkGroup && bulkGroup !== '0'"
                class="px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 rounded hover:bg-indigo-700 disabled:opacity-40">
            Apply
        </button>
        <button type="button" @click="selected = []"
                class="text-sm text-gray-500 hover:text-gray-700">
            Clear selection
        </button>
    </div>

    {{-- Assignment form --}}
    <form method="POST" data-track-changes="true"
          action="{{ route('admin.ministries.groups.assign.save', $ministry) }}">
        @csrf

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="select-all"
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600"
                           @change="toggleAll($event)">
                    <label for="select-all" class="text-sm font-medium text-gray-700">
                        {{ $ministry->activeMembers->count() }} members total
                    </label>
                </div>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save All Assignments
                </button>
            </div>

            <ul class="divide-y divide-gray-100">
                @forelse($ministry->activeMembers as $member)
                <li class="flex items-center px-5 py-3 hover:bg-gray-50 gap-4">
                    {{-- Checkbox for bulk --}}
                    <input type="checkbox"
                           :value="{{ $member->id }}"
                           x-model="selected"
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 flex-shrink-0">

                    {{-- Avatar --}}
                    <div class="flex-shrink-0 h-9 w-9">
                        @if($member->photo_path)
                        <img class="h-9 w-9 rounded-full object-cover"
                             src="{{ asset('storage/' . $member->photo_path) }}" alt="">
                        @else
                        <div class="h-9 w-9 rounded-full bg-gray-100 flex items-center justify-center">
                            <span class="text-gray-500 font-medium text-xs">
                                {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- Name & role --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $member->full_name }}</p>
                        <p class="text-xs text-gray-400">
                            {{ ucfirst(str_replace('_', ' ', $member->pivot->role)) }}
                            @if($member->phone_primary)
                             &middot; {{ $member->phone_primary }}
                            @endif
                        </p>
                    </div>

                    {{-- Group dropdown — bound to Alpine assignments object --}}
                    <div class="flex-shrink-0 w-40">
                        <select name="assignments[{{ $member->id }}]"
                                x-model="assignments[{{ $member->id }}]"
                                @change="assignments[{{ $member->id }}] = $event.target.value ? parseInt($event.target.value) : null"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Unassigned</option>
                            @foreach($groups as $g)
                            <option value="{{ $g->id }}"
                                {{ ($currentAssignments[$member->id] ?? null) == $g->id ? 'selected' : '' }}>
                                {{ $g->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Hidden input mirrors Alpine state for form POST --}}
                    <input type="hidden" name="assignments[{{ $member->id }}]"
                           :value="assignments[{{ $member->id }}] ?? ''">
                </li>
                @empty
                <li class="px-5 py-10 text-center text-sm text-gray-400">
                    No active members in this ministry.
                </li>
                @endforelse
            </ul>

            @if($ministry->activeMembers->count() > 8)
            <div class="px-5 py-3 border-t border-gray-200 flex justify-end">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 shadow-sm">
                    Save All Assignments
                </button>
            </div>
            @endif
        </div>

    </form>

</div>{{-- end x-data --}}
@endif

@endsection

@push('scripts')
<script>
function assignmentManager(initialAssignments, groups, totalMembers) {
    return {
        assignments: Object.fromEntries(
            Object.entries(initialAssignments).map(([k, v]) => [parseInt(k), v ? parseInt(v) : null])
        ),
        groups: groups,
        totalMembers: totalMembers,
        selected: [],
        bulkGroup: '',

        getCount(groupId) {
            return Object.values(this.assignments).filter(v => v === groupId).length;
        },

        getUnassigned() {
            return Object.values(this.assignments).filter(v => !v).length;
        },

        isSmallest(groupId) {
            const counts = this.groups.map(g => this.getCount(g.id));
            const nonZero = counts.filter(c => c > 0);
            if (nonZero.length === 0) return false;
            const min = Math.min(...nonZero);
            return this.getCount(groupId) === min;
        },

        isLargest(groupId) {
            const counts = this.groups.map(g => this.getCount(g.id));
            if (counts.every(c => c === 0)) return false;
            const max = Math.max(...counts);
            return this.getCount(groupId) === max && max > 0;
        },

        toggleAll(e) {
            if (e.target.checked) {
                this.selected = Object.keys(this.assignments).map(Number);
            } else {
                this.selected = [];
            }
        },

        applyBulk() {
            const targetGroup = this.bulkGroup === '0' ? null : parseInt(this.bulkGroup);
            this.selected.forEach(memberId => {
                this.assignments[memberId] = targetGroup;
                // Also update the visible select
                const sel = document.querySelector(`select[name="assignments[${memberId}]"]`);
                if (sel) sel.value = targetGroup ?? '';
            });
            this.selected = [];
            this.bulkGroup = '';
        },
    };
}
</script>
@endpush
