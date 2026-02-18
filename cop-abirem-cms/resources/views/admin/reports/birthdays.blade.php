@extends('layouts.admin')

@section('title', 'Birthday Report')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.reports.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Birthday Report - {{ $monthName }}</h1>
        </div>
        <form action="" method="GET" class="flex items-center space-x-2">
            <select name="month" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endfor
            </select>
        </form>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b">
        <p class="text-sm text-gray-500">{{ $members->count() }} members celebrating birthdays in {{ $monthName }}</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Age</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($members as $member)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 font-bold">
                            {{ $member->birthday_day }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-medium">{{ $member->full_name }}</td>
                    <td class="px-4 py-3 text-sm">{{ $member->phone_number ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">{{ $member->age }} years</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-12 text-center text-gray-500">No birthdays in {{ $monthName }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
