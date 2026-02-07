@extends('layouts.admin')

@section('title', 'SMS Templates')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.sms.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">SMS Templates</h1>
        </div>
        @can('sms.templates')
        <a href="{{ route('admin.sms.templates.create') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            New Template
        </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
        <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">{{ $template->name }}</h3>
                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                            {{ ucfirst($template->category) }}
                        </span>
                    </div>
                    @if(!$template->is_active)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                    @endif
                </div>

                <div class="mt-3 bg-gray-50 rounded p-3 text-sm text-gray-700 min-h-[80px]">
                    {{ Str::limit($template->content, 120) }}
                </div>

                @if(!empty($template->variables))
                <div class="mt-2">
                    <p class="text-xs text-gray-500">
                        Variables: 
                        @foreach($template->variables as $var)
                            <code class="text-indigo-600">{!! '{' . $var . '}' !!}</code>{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </p>
                </div>
                @endif

                <div class="mt-4 flex items-center justify-between">
                    <a href="{{ route('admin.sms.compose', ['template_id' => $template->id]) }}" 
                       class="text-sm text-green-600 hover:text-green-800">Use Template →</a>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.sms.templates.edit', $template) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Edit</a>
                        <form action="{{ route('admin.sms.templates.destroy', $template) }}" method="POST" class="inline"
                              onsubmit="return confirm('Delete this template?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12">
            <p class="text-gray-500">No templates found. Create one to get started.</p>
        </div>
        @endforelse
    </div>
@endsection
