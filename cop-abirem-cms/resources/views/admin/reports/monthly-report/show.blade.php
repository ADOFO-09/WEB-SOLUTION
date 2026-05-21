@extends('layouts.admin')

@section('title', 'Monthly Report — ' . $report->period_label)

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.reports.monthly-report.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Monthly Report — {{ $report->period_label }}</h1>
                <p class="text-sm text-gray-500">
                    @if($report->status === 'submitted')
                        <span class="inline-flex items-center px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-medium">Submitted</span>
                        &nbsp;by {{ $report->submitter?->name ?? '—' }} on {{ $report->submitted_at?->format('d M Y') }}
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full font-medium">Draft</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            @if($report->status === 'draft')
            <a href="{{ route('admin.reports.monthly-report.edit', $report) }}"
               class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-sm font-medium">Edit</a>
            @endif
            <a href="{{ route('admin.reports.monthly-report.print', $report) }}"
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium">Print</a>
            <a href="{{ route('admin.reports.monthly-report.pdf', $report) }}"
               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">Download PDF</a>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
@endif

{{-- ── Summary cards ──────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @php
        $cards = [
            ['label' => 'Total Souls Won',        'value' => $report->total_souls_won,          'color' => 'blue'],
            ['label' => 'Water Baptisms',          'value' => $report->total_water_baptisms,      'color' => 'indigo'],
            ['label' => 'Transfers In',            'value' => $report->total_transfer_in,         'color' => 'green'],
            ['label' => 'Transfers Out',           'value' => $report->total_transfer_out,        'color' => 'orange'],
        ];
    @endphp
    @foreach($cards as $card)
    <div class="bg-white rounded-lg shadow p-5 text-center">
        <p class="text-3xl font-bold text-{{ $card['color'] }}-600">{{ number_format($card['value']) }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ $card['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── Section 2: Leadership ──────────────────────────────────────────── --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <h2 class="font-semibold text-gray-800">Section 2 — Leadership &amp; Transfers</h2>
    </div>
    <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4 border-b">
        @foreach(['Elders' => $report->elders_count, 'Deacons' => $report->deacons_count, 'Deaconesses' => $report->deaconesses_count, 'Other Leaders' => $report->leaders_count] as $label => $val)
        <div class="text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $val }}</p>
            <p class="text-xs text-gray-500">{{ $label }}</p>
        </div>
        @endforeach
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Transfer In (Total: {{ $report->total_transfer_in }})</h3>
            @include('admin.reports.monthly-report._transfer_show', ['prefix' => 'transfer_in', 'report' => $report])
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Transfer Out (Total: {{ $report->total_transfer_out }})</h3>
            @include('admin.reports.monthly-report._transfer_show', ['prefix' => 'transfer_out', 'report' => $report])
        </div>
    </div>
</div>

{{-- ── Section 3: Home Cell & Bible Study ─────────────────────────────── --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <h2 class="font-semibold text-gray-800">Section 3 — Home Cell &amp; Bible Study</h2>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Home Cell</h3>
            @foreach([
                'Opened'           => $report->home_cell_opened,
                'Closed'           => $report->home_cell_closed,
                'Meetings Held'    => $report->home_cell_meetings_held,
                'Male Attendance'  => $report->home_cell_male_attendance,
                'Female Attendance'=> $report->home_cell_female_attendance,
                'Total Attendance' => $report->total_home_cell_attendance,
            ] as $label => $val)
            <div class="flex justify-between py-1 border-b last:border-0 text-sm">
                <span class="text-gray-600">{{ $label }}</span>
                <span class="font-medium">{{ number_format($val) }}</span>
            </div>
            @endforeach
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Bible Study Groups</h3>
            @foreach([
                'Opened'           => $report->bible_study_opened,
                'Closed'           => $report->bible_study_closed,
                'Leaders'          => $report->bible_study_leaders_count,
                'Meetings Held'    => $report->bible_study_meetings_held,
                'Male Attendance'  => $report->bible_study_male_attendance,
                'Female Attendance'=> $report->bible_study_female_attendance,
                'Public Readings'  => $report->bible_study_public_readings,
                'Total Attendance' => $report->total_bible_study_attendance,
            ] as $label => $val)
            <div class="flex justify-between py-1 border-b last:border-0 text-sm">
                <span class="text-gray-600">{{ $label }}</span>
                <span class="font-medium">{{ number_format($val) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Section 4: Outreaches ───────────────────────────────────────────── --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <h2 class="font-semibold text-gray-800">Section 4 — Outreaches &amp; Souls Won (Total: {{ $report->total_souls_won }})</h2>
    </div>
    <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            'Total Outreaches'          => $report->total_outreaches,
            'Adults'                    => $report->souls_adults,
            'Gospel Sunday'             => $report->souls_gospel_sunday,
            'Children'                  => $report->souls_children,
            'Other COP'                 => $report->souls_other_cop,
            'HUM'                       => $report->souls_hum,
            'MPWD'                      => $report->souls_mpwd,
            'Chaplaincy'                => $report->souls_chaplaincy,
            'Chieftaincy'               => $report->souls_chieftaincy,
            'SOM'                       => $report->souls_som,
            'Digital Space'             => $report->souls_digital_space,
            'Backsliders Won Back'      => $report->backsliders_won_back,
            'Backsliders Being Followed'=> $report->backsliders_being_followed,
        ] as $label => $val)
        <div class="text-center bg-gray-50 rounded p-3">
            <p class="text-xl font-bold text-gray-800">{{ number_format($val) }}</p>
            <p class="text-xs text-gray-500">{{ $label }}</p>
        </div>
        @endforeach
    </div>
</div>

{{-- ── Section 5: Baptisms ─────────────────────────────────────────────── --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <h2 class="font-semibold text-gray-800">Section 5 — Baptisms</h2>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Water Baptism (Total: {{ $report->total_water_baptisms }})</h3>
            @include('admin.reports.monthly-report._transfer_show', ['prefix' => 'water_baptism', 'report' => $report])
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Holy Spirit Baptism (Total: {{ $report->total_hs_baptisms }})</h3>
            @foreach(['New Converts' => $report->hs_baptism_new_converts, 'Old Members' => $report->hs_baptism_old_members] as $label => $val)
            <div class="flex justify-between py-1 border-b last:border-0 text-sm">
                <span class="text-gray-600">{{ $label }}</span>
                <span class="font-medium">{{ number_format($val) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Section 6: Life Events ──────────────────────────────────────────── --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <h2 class="font-semibold text-gray-800">Section 6 — Life Events</h2>
    </div>
    <div class="p-6 grid grid-cols-2 md:grid-cols-5 gap-4 text-center">
        @foreach([
            'Births'                  => $report->births,
            'Male Children Dedicated' => $report->male_children_dedicated,
            'Female Children Dedicated'=> $report->female_children_dedicated,
            'Deaths (Children)'       => $report->deaths_children,
            'Deaths (Adults)'         => $report->deaths_adults,
        ] as $label => $val)
        <div class="bg-gray-50 rounded p-3">
            <p class="text-xl font-bold text-gray-800">{{ number_format($val) }}</p>
            <p class="text-xs text-gray-500">{{ $label }}</p>
        </div>
        @endforeach
    </div>
</div>

{{-- ── Section 7: Worship ──────────────────────────────────────────────── --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <h2 class="font-semibold text-gray-800">Section 7 — Worship &amp; Attendance</h2>
    </div>
    <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach([
            'Sunday Morning Attendance'    => $report->sunday_morning_attendance,
            'Communion Sunday Attendance'  => $report->communion_sunday_attendance,
            'Communion Participants'       => $report->communion_participants,
            'New Converts Classes Held'    => $report->new_converts_classes_held,
            'New Converts Attendance'      => $report->new_converts_class_attendance,
            'New Converts Retained'        => $report->new_converts_retained,
            'Elder Visits (New Converts)'  => $report->elder_visits_new_converts,
            'Midweek Teachings'            => $report->midweek_teachings,
            'Midweek Attendance'           => $report->midweek_attendance,
            'Weekly Prayer Meetings'       => $report->weekly_prayer_meetings,
            'Weekly Prayer Attendance'     => $report->weekly_prayer_attendance,
            'Annual Thematic Teachings'    => $report->annual_thematic_teachings,
            'Holy Ghost Prayer Sessions'   => $report->holy_ghost_prayer_sessions,
            'Marriage Teachings'           => $report->marriage_teachings,
            'Blessed Marriages'            => $report->blessed_marriages,
            'Intergenerational Services'   => $report->intergenerational_services,
        ] as $label => $val)
        <div class="text-center bg-gray-50 rounded p-3">
            <p class="text-xl font-bold text-gray-800">{{ number_format($val) }}</p>
            <p class="text-xs text-gray-500">{{ $label }}</p>
        </div>
        @endforeach
    </div>
</div>

{{-- ── Section 8: Social ───────────────────────────────────────────────── --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <h2 class="font-semibold text-gray-800">Section 8 — Social Interventions (Beneficiaries: {{ $report->total_beneficiaries }})</h2>
    </div>
    <div class="p-6 grid grid-cols-2 md:grid-cols-3 gap-3 text-center">
        @foreach([
            'Tertiary Sponsorship'      => $report->social_tertiary_sponsorship,
            'Pre-Tertiary Sponsorship'  => $report->social_pre_tertiary_sponsorship,
            'Health Support'            => $report->social_health_support,
            'Apprenticeship Support'    => $report->social_apprenticeship_support,
            'Community Transformation'  => $report->social_community_transformation,
            'Environmental Care'        => $report->social_environmental_care,
            'Male Beneficiaries'        => $report->beneficiaries_male,
            'Female Beneficiaries'      => $report->beneficiaries_female,
            'Other Beneficiaries'       => $report->beneficiaries_other,
        ] as $label => $val)
        <div class="bg-gray-50 rounded p-3">
            <p class="text-xl font-bold text-gray-800">{{ number_format($val) }}</p>
            <p class="text-xs text-gray-500">{{ $label }}</p>
        </div>
        @endforeach
    </div>
</div>

{{-- ── Section 9: Financial ────────────────────────────────────────────── --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <h2 class="font-semibold text-gray-800">Section 9 — Financial</h2>
    </div>
    <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
        @foreach([
            'Monthly Net Tithes'         => $report->monthly_net_tithes,
            'Missions Offering'          => $report->monthly_missions_offering,
            'Spent (Human Dev.)'         => $report->amount_spent_human_development,
            'Spent (Non-Human)'          => $report->amount_spent_non_human,
        ] as $label => $val)
        <div class="bg-blue-50 rounded p-4">
            <p class="text-2xl font-bold text-blue-800">{{ $currencySymbol }} {{ number_format($val, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $label }}</p>
        </div>
        @endforeach
    </div>
    <div class="px-6 pb-4 text-right">
        <span class="text-sm text-gray-500">Total Amount Spent: </span>
        <span class="font-semibold text-gray-800">{{ $currencySymbol }} {{ number_format($report->total_amount_spent, 2) }}</span>
    </div>
</div>

@if($report->notes)
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="font-semibold text-gray-800 mb-2">Notes / Remarks</h2>
    <p class="text-sm text-gray-600 whitespace-pre-line">{{ $report->notes }}</p>
</div>
@endif

<div class="text-xs text-gray-400 text-right">
    Prepared by: {{ $report->creator?->name ?? '—' }}
    &nbsp;|&nbsp; Last updated: {{ $report->updated_at->format('d M Y H:i') }}
    @if($report->updater && $report->updater->id !== $report->creator?->id)
        by {{ $report->updater->name }}
    @endif
</div>

</div>
@endsection
