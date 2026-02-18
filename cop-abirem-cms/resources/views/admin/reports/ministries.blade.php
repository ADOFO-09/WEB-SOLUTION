@extends('layouts.admin')

@section('title', 'Ministry Report')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.reports.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Ministry Report</h1>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-gray-900">{{ $ministries->count() }}</p>
            <p class="text-sm text-gray-500">Total Ministries</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-blue-600">{{ number_format($totalMembers) }}</p>
            <p class="text-sm text-gray-500">Active Members</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-4xl font-bold text-green-600">{{ number_format($membersInMinistries) }}</p>
            <p class="text-sm text-gray-500">In Ministries</p>
        </div>
    </div>

    <!-- Ministry List -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold">Ministry Membership</h3></div>
        <div class="p-6">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ministry</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Members</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">% of Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($ministries as $ministry)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $ministry->name }}</td>
                        <td class="px-4 py-3 text-right">{{ $ministry->active_members_count }}</td>
                        <td class="px-4 py-3 text-right">
                            {{ $totalMembers > 0 ? round(($ministry->active_members_count / $totalMembers) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
