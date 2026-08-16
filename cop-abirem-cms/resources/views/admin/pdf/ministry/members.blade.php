@extends('admin.pdf.ministry-layout')

@section('pdf-title', $ministry->name . ' — Members')
@section('pdf-meta', 'Total: ' . $members->count() . ' members &nbsp;&bull;&nbsp; ' . now()->format('d M Y'))

@section('content')
<table class="data">
    <thead>
        <tr>
            <th>Member ID</th>
            <th>Name</th>
            <th>Gender</th>
            <th>Phone</th>
            <th>Marital Status</th>
            <th>Status</th>
            <th>Date Joined</th>
        </tr>
    </thead>
    <tbody>
        @forelse($members as $member)
        <tr>
            <td>{{ $member->member_id }}</td>
            <td class="fw-b">{{ $member->full_name }}</td>
            <td class="c">{{ ucfirst($member->gender) }}</td>
            <td>{{ $member->phone_primary }}</td>
            <td class="c">{{ ucfirst($member->marital_status) }}</td>
            <td class="c">
                @if($member->membership_status === 'active')
                    <span class="badge badge-g">Active</span>
                @else
                    <span class="badge badge-y">{{ ucfirst(str_replace('_', ' ', $member->membership_status)) }}</span>
                @endif
            </td>
            <td class="c">{{ $member->date_joined?->format('d M Y') }}</td>
        </tr>
        @empty
        <tr class="empty"><td colspan="7">No members found.</td></tr>
        @endforelse
        @if($members->count())
        <tr class="total">
            <td colspan="6">Total Members</td>
            <td class="r">{{ $members->count() }}</td>
        </tr>
        @endif
    </tbody>
</table>
@endsection
