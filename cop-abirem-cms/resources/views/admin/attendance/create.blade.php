@extends('layouts.admin')

@section('title', 'Create Attendance Session')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.attendance.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Create Attendance Session</h1>
    </div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    @if($openSessions->count() > 0)
    <!-- Open Sessions Warning -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">You have open sessions</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc list-inside">
                        @foreach($openSessions as $session)
                        <li>
                            <a href="{{ route('admin.attendance.mark', $session) }}" class="underline">
                                {{ $session->serviceType->name ?? 'Service' }} - {{ $session->service_date->format('M d, Y') }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.attendance.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Session Details</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="service_type_id" class="block text-sm font-medium text-gray-700">Service Type *</label>
                    <select name="service_type_id" id="service_type_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            onchange="updateServiceTime(this)">
                        <option value="">Select service type</option>
                        @foreach($serviceTypes as $type)
                            <option value="{{ $type->id }}" 
                                    data-time="{{ $type->default_start_time?->format('H:i') }}"
                                    {{ old('service_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ $type->day_of_week ?? 'Any day' }})
                            </option>
                        @endforeach
                    </select>
                    @error('service_type_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="ministry_id" class="block text-sm font-medium text-gray-700">Ministry (Optional)</label>
                    <select name="ministry_id" id="ministry_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Main Service</option>
                        @foreach($ministries as $ministry)
                            <option value="{{ $ministry->id }}" {{ old('ministry_id') == $ministry->id ? 'selected' : '' }}>
                                {{ $ministry->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="service_date" class="block text-sm font-medium text-gray-700">Service Date *</label>
                    <input type="date" name="service_date" id="service_date" 
                           value="{{ old('service_date', date('Y-m-d')) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('service_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time *</label>
                    <input type="time" name="start_time" id="start_time" 
                           value="{{ old('start_time', '09:00') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('start_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="theme" class="block text-sm font-medium text-gray-700">Theme/Topic</label>
                    <input type="text" name="theme" id="theme" value="{{ old('theme') }}"
                           placeholder="e.g., Walking in Faith"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="preacher" class="block text-sm font-medium text-gray-700">Preacher/Speaker</label>
                    <input type="text" name="preacher" id="preacher" value="{{ old('preacher') }}"
                           placeholder="e.g., Pastor John Mensah"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.attendance.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                Create & Start Marking
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function updateServiceTime(select) {
    const option = select.options[select.selectedIndex];
    const time = option.dataset.time;
    if (time) {
        document.getElementById('start_time').value = time;
    }
}
</script>
@endpush
@endsection
