@extends('layouts.admin')

@section('title', 'Donations')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Donations</h1>
        @can('finance.create')
        <a href="{{ route('admin.donations.create') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Record Donation
        </a>
        @endcan
    </div>
@endsection

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm font-medium text-gray-500">Total This Year</div>
        <div class="mt-2 text-2xl font-bold text-gray-900">GH₵ {{ number_format($stats['total_amount'], 2) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm font-medium text-gray-500">This Month</div>
        <div class="mt-2 text-2xl font-bold text-indigo-600">GH₵ {{ number_format($stats['this_month'], 2) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm font-medium text-gray-500">Total Donations</div>
        <div class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['total_count'] }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm font-medium text-gray-500">In-Kind Donations</div>
        <div class="mt-2 text-2xl font-bold text-green-600">{{ $stats['in_kind_count'] }}</div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-4">
        <form action="{{ route('admin.donations.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <select name="project_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="donation_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">All Types</option>
                    <option value="cash" {{ request('donation_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="in_kind" {{ request('donation_type') == 'in_kind' ? 'selected' : '' }}>In-Kind</option>
                </select>
            </div>
            <div>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Filter</button>
                <a href="{{ route('admin.donations.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Donations Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Receipt #</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Donor</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($donations as $donation)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                    {{ $donation->receipt_number ?? $donation->reference_number }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($donation->is_anonymous)
                    <span class="text-gray-500 italic">Anonymous</span>
                    @else
                    <div class="text-sm font-medium text-gray-900">
                        {{ $donation->member->full_name ?? $donation->donor_name ?? 'Unknown' }}
                    </div>
                    @if($donation->member)
                    <div class="text-xs text-gray-500">{{ $donation->member->member_id }}</div>
                    @endif
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($donation->donation_type == 'cash')
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Cash</span>
                    @else
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">In-Kind</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    @if($donation->donation_type == 'cash')
                    GH₵ {{ number_format($donation->amount, 2) }}
                    @else
                    {{ Str::limit($donation->in_kind_description, 30) }}
                    @if($donation->estimated_value)
                    <div class="text-xs text-gray-500">Est: GH₵ {{ number_format($donation->estimated_value, 2) }}</div>
                    @endif
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $donation->project->name ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ \Carbon\Carbon::parse($donation->payment_date)->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('admin.donations.show', $donation) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                        @can('finance.edit')
                        <a href="{{ route('admin.donations.edit', $donation) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                        @endcan
                        <a href="{{ route('admin.donations.receipt', $donation) }}" class="text-green-600 hover:text-green-900" target="_blank">Receipt</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    No donations found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($donations->hasPages())
    <div class="px-6 py-4 border-t">
        {{ $donations->links() }}
    </div>
    @endif
</div>
@endsection
