@extends('layouts.admin')

@section('title', 'Reports')

@section('header')
    <h1 class="text-2xl font-bold text-gray-900">Reports Center</h1>
@endsection

@section('content')
<div class="space-y-8">
    @forelse($reportCategories as $category => $reports)
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-4 capitalize">{{ $category }} Reports</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($reports as $report)
            <a href="{{ route($report['route']) }}"
               class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-indigo-200 transition group">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-medium text-gray-900 group-hover:text-indigo-600">{{ $report['name'] }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $report['description'] }}</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @empty
    <div class="text-center py-16">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-600 mb-1">No Reports Available</h3>
        <p class="text-sm text-gray-400">You don't have permission to view any reports. Contact your administrator.</p>
    </div>
    @endforelse
</div>
@endsection
