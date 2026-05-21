@extends('layouts.admin')

@section('title', 'Member Directory')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.reports.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Member Directory</h1>
        </div>
        <div class="flex items-center space-x-2">
            <form action="{{ route('admin.reports.member-directory') }}" method="GET" class="flex items-center space-x-2">
                <select name="ministry_id" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                    <option value="">All Ministries</option>
                    @foreach($ministries as $ministry)
                        <option value="{{ $ministry->id }}" {{ request('ministry_id') == $ministry->id ? 'selected' : '' }}>{{ $ministry->name }}</option>
                    @endforeach
                </select>
            </form>
            <button onclick="window.print()" class="px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">Print</button>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h3 class="text-lg font-semibold">{{ $members->count() }} Members</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Member ID</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Gender</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($members as $member)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $member->full_name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $member->member_id }}</td>
                    <td class="px-4 py-3 text-sm">{{ $member->phone_number ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">{{ $member->email ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm capitalize">{{ $member->gender ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .nav-section, form, button, a[href*="reports.index"] { display: none !important; }
    .bg-white { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
}
</style>
@endpush
