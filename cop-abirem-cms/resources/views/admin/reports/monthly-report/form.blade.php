@extends('layouts.admin')

@section('title', isset($monthlyReport) ? 'Edit Monthly Report' : 'New Monthly Report')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.reports.monthly-report.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">
            {{ isset($monthlyReport) ? 'Edit Report — ' . $monthlyReport->period_label : 'New Monthly Report' }}
        </h1>
    </div>
@endsection

@section('content')
@php
    $action = isset($monthlyReport)
        ? route('admin.reports.monthly-report.update', $monthlyReport)
        : route('admin.reports.monthly-report.store');
    $method = isset($monthlyReport) ? 'PUT' : 'POST';

    // Helper: get value with old() fallback
    $val = fn(string $key) => old($key, $prefill[$key] ?? 0);

    // Fields that are auto-populated from live system data
    $autoFields = [
        'elders_count','deacons_count','deaconesses_count','leaders_count',
        'transfer_in_children_male','transfer_in_children_female',
        'transfer_in_teens_male','transfer_in_teens_female',
        'transfer_in_young_adults_male','transfer_in_young_adults_female',
        'transfer_in_adults_male','transfer_in_adults_female',
        'transfer_out_children_male','transfer_out_children_female',
        'transfer_out_teens_male','transfer_out_teens_female',
        'transfer_out_young_adults_male','transfer_out_young_adults_female',
        'transfer_out_adults_male','transfer_out_adults_female',
        'home_cell_opened','home_cell_closed','home_cell_meetings_held',
        'home_cell_male_attendance','home_cell_female_attendance',
        'bible_study_opened','bible_study_closed','bible_study_leaders_count',
        'bible_study_meetings_held','bible_study_male_attendance','bible_study_female_attendance',
        'bible_study_public_readings',
        'souls_adults','souls_gospel_sunday','souls_children',
        'water_baptism_children_male','water_baptism_children_female',
        'water_baptism_teens_male','water_baptism_teens_female',
        'water_baptism_young_adults_male','water_baptism_young_adults_female',
        'water_baptism_adults_male','water_baptism_adults_female',
        'hs_baptism_new_converts',
        'deaths_children','deaths_adults',
        'sunday_morning_attendance','communion_sunday_attendance','communion_participants',
        'new_converts_retained',
        'midweek_teachings','midweek_attendance',
        'weekly_prayer_meetings','weekly_prayer_attendance',
        'monthly_net_tithes','monthly_missions_offering',
        'amount_spent_human_development','amount_spent_non_human',
    ];

    // CSS classes for auto vs manual input fields
    $autoCls   = 'w-full rounded-md border-blue-300 bg-blue-50 text-sm text-center ring-1 ring-blue-200 focus:ring-blue-400';
    $manualCls = 'w-full rounded-md border-gray-300 text-sm text-center';
    $autoFinCls   = 'w-full rounded-md border-blue-300 bg-blue-50 text-sm text-right ring-1 ring-blue-200 focus:ring-blue-400';
    $manualFinCls = 'w-full rounded-md border-gray-300 text-sm text-right';

    $cls    = fn(string $key) => in_array($key, $autoFields) ? $autoCls    : $manualCls;
    $finCls = fn(string $key) => in_array($key, $autoFields) ? $autoFinCls : $manualFinCls;
@endphp

<form action="{{ $action }}" method="POST" id="monthly-report-form">
    @csrf
    @method($method)

    @if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
        <strong>Please fix the errors below:</strong>
        <ul class="mt-1 ml-4 list-disc text-sm">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Legend --}}
    <div class="flex items-center gap-6 mb-4 p-3 bg-white rounded-lg shadow text-sm">
        <span class="font-medium text-gray-700">Field key:</span>
        <span class="flex items-center gap-2">
            <span class="inline-block w-4 h-4 rounded bg-blue-100 border border-blue-300"></span>
            <span class="text-blue-700 font-medium">Auto-filled from system data</span>
            <span class="text-gray-500">— review and adjust if needed</span>
        </span>
        <span class="flex items-center gap-2">
            <span class="inline-block w-4 h-4 rounded bg-white border border-gray-300"></span>
            <span class="text-gray-700 font-medium">Manual entry required</span>
        </span>
    </div>

    {{-- ── Period selector ─────────────────────────────────────────── --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Report Period</h2>
        <div class="grid grid-cols-2 gap-4 max-w-sm">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                <select name="month" class="w-full rounded-md border-gray-300 text-sm"
                    {{ isset($monthlyReport) ? 'disabled' : '' }}>
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @if(isset($monthlyReport))
                    <input type="hidden" name="month" value="{{ $monthlyReport->month }}">
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <select name="year" class="w-full rounded-md border-gray-300 text-sm"
                    {{ isset($monthlyReport) ? 'disabled' : '' }}>
                    @for($y = now()->year + 1; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                @if(isset($monthlyReport))
                    <input type="hidden" name="year" value="{{ $monthlyReport->year }}">
                @endif
            </div>
        </div>
    </div>

    {{-- ── Section 2: Leadership ───────────────────────────────────── --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Section 2 — Leadership</h2>
        <p class="text-xs text-gray-500 mb-4">Counted from active members with matching titles and ministry leadership roles.</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach(['elders_count' => 'Elders', 'deacons_count' => 'Deacons', 'deaconesses_count' => 'Deaconesses', 'leaders_count' => 'Leaders (all roles)'] as $field => $label)
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
                <input type="number" name="{{ $field }}" value="{{ $val($field) }}" min="0"
                       class="{{ $cls($field) }}">
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Transfer In <span class="text-blue-600 text-xs font-normal">(auto: from date_joined this month)</span></h3>
                @include('admin.reports.monthly-report._transfer_grid', ['prefix' => 'transfer_in', 'val' => $val, 'cls' => $cls])
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Transfer Out <span class="text-blue-600 text-xs font-normal">(auto: from date_left this month)</span></h3>
                @include('admin.reports.monthly-report._transfer_grid', ['prefix' => 'transfer_out', 'val' => $val, 'cls' => $cls])
            </div>
        </div>
    </div>

    {{-- ── Section 3: Home Cell & Bible Study ─────────────────────── --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Section 3 — Home Cell &amp; Bible Study Groups</h2>
        <p class="text-xs text-gray-500 mb-4">
            Auto-filled from ministries typed as <strong>Home Cell</strong> or <strong>Bible Study Group</strong>.
            <a href="{{ route('admin.ministries.index') }}" class="text-blue-600 hover:underline" target="_blank">Manage ministry types →</a>
        </p>

        <h3 class="text-sm font-semibold text-gray-700 mb-3">Home Cell</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            @foreach([
                'home_cell_opened'            => 'Opened',
                'home_cell_closed'            => 'Closed',
                'home_cell_meetings_held'     => 'Meetings Held',
                'home_cell_male_attendance'   => 'Male Attendance',
                'home_cell_female_attendance' => 'Female Attendance',
            ] as $field => $label)
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
                <input type="number" name="{{ $field }}" value="{{ $val($field) }}" min="0"
                       class="{{ $cls($field) }}">
            </div>
            @endforeach
        </div>

        <h3 class="text-sm font-semibold text-gray-700 mb-3">Bible Study Groups</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                'bible_study_opened'            => 'Opened',
                'bible_study_closed'            => 'Closed',
                'bible_study_leaders_count'     => 'Leaders',
                'bible_study_meetings_held'     => 'Meetings Held',
                'bible_study_male_attendance'   => 'Male Attendance',
                'bible_study_female_attendance' => 'Female Attendance',
                'bible_study_public_readings'   => 'Public Bible Readings',
            ] as $field => $label)
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
                <input type="number" name="{{ $field }}" value="{{ $val($field) }}" min="0"
                       class="{{ $cls($field) }}">
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Section 4: Outreaches & Souls Won ──────────────────────── --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Section 4 — Outreaches &amp; Souls Won</h2>
        <p class="text-xs text-gray-500 mb-4">Souls won (adults/children/gospel Sunday) counted from visitor conversions this month. Other fields require manual entry.</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                'total_outreaches'           => 'Total Outreaches',
                'souls_adults'               => 'Adults (non-CoP)',
                'souls_gospel_sunday'        => 'Gospel Sunday',
                'souls_children'             => 'Children',
                'souls_other_cop'            => 'Other CoP',
                'souls_hum'                  => 'HUM',
                'souls_mpwd'                 => 'MPWD',
                'souls_chaplaincy'           => 'Chaplaincy',
                'souls_chieftaincy'          => 'Chieftaincy',
                'souls_som'                  => 'SOM',
                'souls_digital_space'        => 'Digital Space',
                'backsliders_won_back'       => 'Backsliders Won Back',
                'backsliders_being_followed' => 'Backsliders Being Followed',
            ] as $field => $label)
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
                <input type="number" name="{{ $field }}" value="{{ $val($field) }}" min="0"
                       class="{{ $cls($field) }}">
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Section 5: Baptisms ────────────────────────────────────── --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Section 5 — Baptisms</h2>
        <p class="text-xs text-gray-500 mb-4">Pulled from members whose baptism date falls in this month, grouped by age and gender.</p>

        <h3 class="text-sm font-semibold text-gray-700 mb-3">Water Baptism <span class="text-blue-600 text-xs font-normal">(auto)</span></h3>
        @include('admin.reports.monthly-report._baptism_grid', ['prefix' => 'water_baptism', 'val' => $val, 'cls' => $cls])

        <h3 class="text-sm font-semibold text-gray-700 mt-4 mb-3">Holy Spirit Baptism</h3>
        <div class="grid grid-cols-2 gap-4 max-w-xs">
            @foreach(['hs_baptism_new_converts' => 'New Converts', 'hs_baptism_old_members' => 'Old Members'] as $field => $label)
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
                <input type="number" name="{{ $field }}" value="{{ $val($field) }}" min="0"
                       class="{{ $cls($field) }}">
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Section 6: Life Events ──────────────────────────────────── --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Section 6 — Life Events</h2>
        <p class="text-xs text-gray-500 mb-4">Deaths auto-filled from members with status "Deceased" and date_left this month. Births and dedications require manual entry.</p>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach([
                'births'                    => 'Births',
                'male_children_dedicated'   => 'Male Children Dedicated',
                'female_children_dedicated' => 'Female Children Dedicated',
                'deaths_children'           => 'Deaths (Children)',
                'deaths_adults'             => 'Deaths (Adults)',
            ] as $field => $label)
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
                <input type="number" name="{{ $field }}" value="{{ $val($field) }}" min="0"
                       class="{{ $cls($field) }}">
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Section 7: Worship & Attendance ────────────────────────── --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Section 7 — Worship &amp; Attendance</h2>
        <p class="text-xs text-gray-500 mb-4">Attendance figures drawn from service sessions recorded this month.</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                'sunday_morning_attendance'     => 'Sunday Morning Attendance',
                'communion_sunday_attendance'   => 'Communion Sunday Attendance',
                'communion_participants'        => 'Communion Participants',
                'new_converts_classes_held'     => 'New Converts Classes Held',
                'new_converts_class_attendance' => 'New Converts Class Attendance',
                'new_converts_retained'         => 'New Converts Retained',
                'elder_visits_new_converts'     => 'Elder Visits (New Converts)',
                'midweek_teachings'             => 'Midweek Teachings',
                'midweek_attendance'            => 'Midweek Attendance',
                'weekly_prayer_meetings'        => 'Weekly Prayer Meetings',
                'weekly_prayer_attendance'      => 'Weekly Prayer Attendance',
                'annual_thematic_teachings'     => 'Annual Thematic Teachings',
                'holy_ghost_prayer_sessions'    => 'Holy Ghost Prayer Sessions',
                'marriage_teachings'            => 'Marriage Teachings',
                'blessed_marriages'             => 'Blessed Marriages',
                'intergenerational_services'    => 'Intergenerational Services',
            ] as $field => $label)
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
                <input type="number" name="{{ $field }}" value="{{ $val($field) }}" min="0"
                       class="{{ $cls($field) }}">
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Section 8: Social Interventions ────────────────────────── --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Section 8 — Social Interventions</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach([
                'social_tertiary_sponsorship'     => 'Tertiary Sponsorship',
                'social_pre_tertiary_sponsorship' => 'Pre-Tertiary Sponsorship',
                'social_health_support'           => 'Health Support',
                'social_apprenticeship_support'   => 'Apprenticeship Support',
                'social_community_transformation' => 'Community Transformation',
                'social_environmental_care'       => 'Environmental Care',
                'beneficiaries_male'              => 'Male Beneficiaries',
                'beneficiaries_female'            => 'Female Beneficiaries',
                'beneficiaries_other'             => 'Other Beneficiaries',
            ] as $field => $label)
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
                <input type="number" name="{{ $field }}" value="{{ $val($field) }}" min="0"
                       class="{{ $cls($field) }}">
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Section 9: Financial ────────────────────────────────────── --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Section 9 — Financial</h2>
        <p class="text-xs text-gray-500 mb-4">Tithes and offerings auto-filled from finance records. Expenses split by category (human development = Welfare &amp; Ministry Support).</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                'monthly_net_tithes'             => 'Monthly Net Tithes ({{ $currencySymbol }})',
                'monthly_missions_offering'      => 'Missions Offering ({{ $currencySymbol }})',
                'amount_spent_human_development' => 'Spent — Human Dev. ({{ $currencySymbol }})',
                'amount_spent_non_human'         => 'Spent — Non-Human ({{ $currencySymbol }})',
            ] as $field => $label)
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
                <input type="number" step="0.01" name="{{ $field }}" value="{{ $val($field) }}" min="0"
                       class="{{ $finCls($field) }}">
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Notes ───────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Notes / Remarks</h2>
        <textarea name="notes" rows="4" maxlength="2000"
                  class="w-full rounded-md border-gray-300 text-sm"
                  placeholder="Any additional observations or context for this month...">{{ old('notes', $prefill['notes'] ?? '') }}</textarea>
    </div>

    {{-- ── Action buttons ──────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.reports.monthly-report.index') }}"
           class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium">
            Cancel
        </a>
        <div class="flex space-x-3">
            <button type="submit" name="draft" value="1"
                    class="px-5 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium">
                Save as Draft
            </button>
            <button type="submit" name="submit" value="1"
                    class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium"
                    onclick="return confirm('Submit this report? Submitted reports cannot be edited.')">
                Submit Report
            </button>
        </div>
    </div>
</form>
@endsection
