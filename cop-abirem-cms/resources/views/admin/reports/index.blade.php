@extends('layouts.admin')

@section('title', 'Reports')

@section('header')
    <h1 class="text-2xl font-bold text-gray-900">Reports Center</h1>
@endsection

@section('content')
<div class="space-y-8">
    @foreach($reportCategories as $category => $reports)
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
    @endforeach
</div>
@endsection
