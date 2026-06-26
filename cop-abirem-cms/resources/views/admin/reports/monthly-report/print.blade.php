<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Report — {{ $report->period_label }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 9.5px;
            color: #000;
            background: #fff;
        }

        .page {
            width: 210mm;
            max-width: 210mm;
            margin: 0 auto;
            padding: 8mm 12mm;
        }

        /* ── Header ──────────────────────────────────────────── */
        .doc-header {
            text-align: center;
            margin-bottom: 5px;
        }
        .doc-header h1 {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-header h2 {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .doc-header .local-line {
            font-size: 9.5px;
            margin-top: 3px;
            font-weight: bold;
        }
        .doc-header .local-line span {
            display: inline-block;
            min-width: 100px;
            border-bottom: 1px solid #000;
        }

        /* ── Main table ──────────────────────────────────────── */
        table.form-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.form-table col.c-cat  { width: 22%; }
        table.form-table col.c-ind  { width: 34%; }
        table.form-table col.c-sub  { width: 14%; }
        table.form-table col.c-val  { width: 30%; }

        table.form-table th,
        table.form-table td {
            border: 0.6px solid #555;
            padding: 2px 4px;
            vertical-align: middle;
            line-height: 1.3;
        }

        /* Column header row */
        .th-main {
            background: #fff;
            font-weight: bold;
            font-size: 9.5px;
            text-align: center;
        }
        .th-month {
            background: #fff;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
            text-transform: uppercase;
        }

        /* Section divider rows (Demographics, Operational Stats, etc.) */
        .sec-hdr td {
            background: #fff;
            font-weight: bold;
            font-size: 9.5px;
            text-align: center;
            padding: 2px 0;
            border-left: none;
            border-right: none;
            border-top: 0.6px solid #555;
            border-bottom: 0.6px solid #555;
        }

        /* Category label cell (left column) */
        .cat {
            font-weight: bold;
            font-size: 9px;
            text-align: center;
            vertical-align: middle;
            padding: 3px 4px;
        }

        /* Indicator cell */
        .ind {
            font-size: 9px;
            padding: 2px 5px;
        }

        /* Sub-indicator cell (Male/Female/Total) */
        .sub {
            font-size: 9px;
            padding: 2px 4px;
            font-style: italic;
        }

        /* Value cell */
        .val {
            font-size: 9px;
            text-align: right;
            padding: 2px 6px;
            font-weight: bold;
            color: #1a3c6e;
        }

        /* Total row */
        .total-row td { background: #e8f0fe; font-weight: bold; }

        /* No-print toolbar */
        .no-print {
            margin-bottom: 8px;
            display: flex;
            gap: 8px;
        }
        .no-print button, .no-print a {
            padding: 5px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-print { background: #1a3c6e; color: #fff; }
        .btn-back  { background: #666; color: #fff; }

        /* Footer */
        .doc-footer {
            margin-top: 6mm;
            font-size: 8px;
            color: #555;
            display: flex;
            justify-content: space-between;
        }

        /* Signature area */
        .sig-area {
            margin-top: 8mm;
            display: flex;
            justify-content: space-between;
        }
        .sig-block { text-align: center; }
        .sig-line {
            display: block;
            width: 60mm;
            border-bottom: 1px solid #000;
            margin: 0 auto 2px;
        }
        .sig-label { font-size: 8px; }

        @media print {
            @page { size: A4 portrait; margin: 8mm 12mm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ── Print toolbar ───────────────────────────────────────── --}}
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">&#128424; Print</button>
        <a class="btn-back" href="{{ route('admin.reports.monthly-report.show', $report) }}">&#8592; Back</a>
    </div>

    {{-- ── Document Header ─────────────────────────────────────── --}}
    <div class="doc-header">
        <h1>{{ \App\Helpers\SettingHelper::reportHeader() ?: \App\Helpers\SettingHelper::churchName() }}</h1>
        <h2>Local Assembly Monthly Report Form</h2>
        <div class="local-line">
            LOCAL:&nbsp;<span>{{ \App\Models\Setting::get('church_name', '') }}</span>
            &nbsp;&nbsp;&nbsp;&nbsp;
            YEAR:&nbsp;<span>{{ $report->year }}</span>
        </div>
    </div>

    {{-- ── Main Form Table ─────────────────────────────────────── --}}
    <table class="form-table" style="margin-top:5px;">
        <colgroup>
            <col class="c-cat">
            <col class="c-ind">
            <col class="c-sub">
            <col class="c-val">
        </colgroup>

        {{-- Column headers --}}
        <tr>
            <td class="th-main" style="border:0.6px solid #555;"></td>
            <td class="th-main" colspan="2" style="border:0.6px solid #555;">Operational Indicators</td>
            <td class="th-month" style="border:0.6px solid #555;">{{ strtoupper($report->month_name) }}</td>
        </tr>

        {{-- ══ DEMOGRAPHICS ══════════════════════════════════════ --}}
        <tr class="sec-hdr"><td colspan="4">Demographics</td></tr>

        {{-- Leadership --}}
        <tr>
            <td class="cat" rowspan="4">Leadership</td>
            <td class="ind" colspan="2">Elders</td>
            <td class="val">{{ $report->elders_count ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Deacons</td>
            <td class="val">{{ $report->deacons_count ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Deaconess</td>
            <td class="val">{{ $report->deaconesses_count ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Leader</td>
            <td class="val">{{ $report->leaders_count ?: '' }}</td>
        </tr>

        {{-- Transfer In --}}
        @php
            $tiAges = [
                'children'     => 'Children transfer',
                'teens'        => 'Teen 13-19yrs',
                'young_adults' => 'Young Adults (20-35)',
                'adults'       => 'Other Adults (35 &amp; Above)',
            ];
            $tiTotalM = $report->transfer_in_children_male + $report->transfer_in_teens_male + $report->transfer_in_young_adults_male + $report->transfer_in_adults_male;
            $tiTotalF = $report->transfer_in_children_female + $report->transfer_in_teens_female + $report->transfer_in_young_adults_female + $report->transfer_in_adults_female;
        @endphp
        <tr>
            <td class="cat" rowspan="{{ count($tiAges) * 3 }}">TRANSFER IN<br>(CHILDREN &amp; ADULT)</td>
            <td class="ind" rowspan="3">Children transfer</td>
            <td class="sub">(Male)</td>
            <td class="val">{{ $report->transfer_in_children_male ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">(Female)</td>
            <td class="val">{{ $report->transfer_in_children_female ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">total</td>
            <td class="val">{{ ($report->transfer_in_children_male + $report->transfer_in_children_female) ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" rowspan="3">Teen 13-19yrs</td>
            <td class="sub">Male</td>
            <td class="val">{{ $report->transfer_in_teens_male ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">Female</td>
            <td class="val">{{ $report->transfer_in_teens_female ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">total</td>
            <td class="val">{{ ($report->transfer_in_teens_male + $report->transfer_in_teens_female) ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" rowspan="3">Young Adults (20-35)</td>
            <td class="sub">Male</td>
            <td class="val">{{ $report->transfer_in_young_adults_male ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">Female</td>
            <td class="val">{{ $report->transfer_in_young_adults_female ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">total</td>
            <td class="val">{{ ($report->transfer_in_young_adults_male + $report->transfer_in_young_adults_female) ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" rowspan="3">Other Adults (35 &amp; Above)</td>
            <td class="sub">male</td>
            <td class="val">{{ $report->transfer_in_adults_male ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">female</td>
            <td class="val">{{ $report->transfer_in_adults_female ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">total</td>
            <td class="val">{{ ($report->transfer_in_adults_male + $report->transfer_in_adults_female) ?: '' }}</td>
        </tr>

        {{-- Transfer Out --}}
        <tr>
            <td class="cat" rowspan="12">TRANSFER OUT<br>(CHILDREN &amp; ADULT)</td>
            <td class="ind" rowspan="3">Children</td>
            <td class="sub">male</td>
            <td class="val">{{ $report->transfer_out_children_male ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">Female</td>
            <td class="val">{{ $report->transfer_out_children_female ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">total</td>
            <td class="val">{{ ($report->transfer_out_children_male + $report->transfer_out_children_female) ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" rowspan="3">Teen 13-19yrs</td>
            <td class="sub">Male</td>
            <td class="val">{{ $report->transfer_out_teens_male ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">Female</td>
            <td class="val">{{ $report->transfer_out_teens_female ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">total</td>
            <td class="val">{{ ($report->transfer_out_teens_male + $report->transfer_out_teens_female) ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" rowspan="3">Young Adults (20-35)</td>
            <td class="sub">Male</td>
            <td class="val">{{ $report->transfer_out_young_adults_male ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">Female</td>
            <td class="val">{{ $report->transfer_out_young_adults_female ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">total</td>
            <td class="val">{{ ($report->transfer_out_young_adults_male + $report->transfer_out_young_adults_female) ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" rowspan="3">Other Adults (35 &amp; above)</td>
            <td class="sub">male</td>
            <td class="val">{{ $report->transfer_out_adults_male ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">Female</td>
            <td class="val">{{ $report->transfer_out_adults_female ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">total</td>
            <td class="val">{{ ($report->transfer_out_adults_male + $report->transfer_out_adults_female) ?: '' }}</td>
        </tr>

        {{-- ══ OPERATIONAL STATS ══════════════════════════════════ --}}
        <tr class="sec-hdr"><td colspan="4">Operational Stats</td></tr>

        {{-- Home Cell --}}
        <tr>
            <td class="cat" rowspan="5">Home cell</td>
            <td class="ind" colspan="2">Opened</td>
            <td class="val">{{ $report->home_cell_opened ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Closed</td>
            <td class="val">{{ $report->home_cell_closed ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Meetings held</td>
            <td class="val">{{ $report->home_cell_meetings_held ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Male attendance</td>
            <td class="val">{{ $report->home_cell_male_attendance ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Female Attendance</td>
            <td class="val">{{ $report->home_cell_female_attendance ?: '' }}</td>
        </tr>

        {{-- Bible Study Groups --}}
        <tr>
            <td class="cat" rowspan="7">Bible Study groups</td>
            <td class="ind" colspan="2">Opened</td>
            <td class="val">{{ $report->bible_study_opened ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Closed</td>
            <td class="val">{{ $report->bible_study_closed ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Number of leaders</td>
            <td class="val">{{ $report->bible_study_leaders_count ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Meetings held</td>
            <td class="val">{{ $report->bible_study_meetings_held ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Male attendance</td>
            <td class="val">{{ $report->bible_study_male_attendance ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Female attendance</td>
            <td class="val">{{ $report->bible_study_female_attendance ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Public Bible readings</td>
            <td class="val">{{ $report->bible_study_public_readings ?: '' }}</td>
        </tr>

        {{-- ══ OUTREACHES AND SOULS WON ══════════════════════════ --}}
        <tr class="sec-hdr"><td colspan="4">Outreaches and Souls Won</td></tr>

        {{-- Souls Won by Category --}}
        <tr>
            <td class="cat" rowspan="5">Souls won by Category</td>
            <td class="ind" colspan="2">Total outreaches (rallies, house-to-house, etc)</td>
            <td class="val">{{ $report->total_outreaches ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Adults Souls won (non-CoP)</td>
            <td class="val">{{ $report->souls_adults ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Gospel Sunday Souls</td>
            <td class="val">{{ $report->souls_gospel_sunday ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Children Souls</td>
            <td class="val">{{ $report->souls_children ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Other CoP souls</td>
            <td class="val">{{ $report->souls_other_cop ?: '' }}</td>
        </tr>

        {{-- Special Ministry Souls --}}
        <tr>
            <td class="cat" rowspan="6">Special Ministry Souls</td>
            <td class="ind" colspan="2">HUM souls</td>
            <td class="val">{{ $report->souls_hum ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">MPWD souls</td>
            <td class="val">{{ $report->souls_mpwd ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Chaplaincy Souls</td>
            <td class="val">{{ $report->souls_chaplaincy ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Chieftaincy Souls</td>
            <td class="val">{{ $report->souls_chieftaincy ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">SOM souls</td>
            <td class="val">{{ $report->souls_som ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Digital Space souls</td>
            <td class="val">{{ $report->souls_digital_space ?: '' }}</td>
        </tr>

        {{-- Follow-up & Retention --}}
        <tr>
            <td class="cat" rowspan="2">Follow up &amp;<br>Retention</td>
            <td class="ind" colspan="2">Backsliders won back</td>
            <td class="val">{{ $report->backsliders_won_back ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Backsliders Being Followed</td>
            <td class="val">{{ $report->backsliders_being_followed ?: '' }}</td>
        </tr>

        {{-- ══ WATER BAPTISM AND HOLY SPIRIT BAPTISM ═════════════ --}}
        <tr class="sec-hdr"><td colspan="4">Water Baptism and Holy Spirit Baptism</td></tr>

        {{-- Water Baptism --}}
        <tr>
            <td class="cat" rowspan="8">Water Baptism</td>
            <td class="ind" rowspan="2">Children 13 years</td>
            <td class="sub">(Male)</td>
            <td class="val">{{ $report->water_baptism_children_male ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">(Female)</td>
            <td class="val">{{ $report->water_baptism_children_female ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" rowspan="2">Teens 13-19yrs</td>
            <td class="sub">(Male)</td>
            <td class="val">{{ $report->water_baptism_teens_male ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">(Female)</td>
            <td class="val">{{ $report->water_baptism_teens_female ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" rowspan="2">Young Adults 20-35yrs</td>
            <td class="sub">Male</td>
            <td class="val">{{ $report->water_baptism_young_adults_male ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">(Female)</td>
            <td class="val">{{ $report->water_baptism_young_adults_female ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" rowspan="2">Adults above 35yrs</td>
            <td class="sub">(Male)</td>
            <td class="val">{{ $report->water_baptism_adults_male ?: '' }}</td>
        </tr>
        <tr>
            <td class="sub">(Female)</td>
            <td class="val">{{ $report->water_baptism_adults_female ?: '' }}</td>
        </tr>

        {{-- Holy Spirit Baptism --}}
        <tr>
            <td class="cat" rowspan="2">Holy Spirit baptism</td>
            <td class="ind" colspan="2">New Converts baptized in H.S</td>
            <td class="val">{{ $report->hs_baptism_new_converts ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Old Members now baptized in H.S</td>
            <td class="val">{{ $report->hs_baptism_old_members ?: '' }}</td>
        </tr>

        {{-- Life Events --}}
        <tr>
            <td class="cat" rowspan="5">Life Events</td>
            <td class="ind" colspan="2">Birth</td>
            <td class="val">{{ $report->births ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Male Children Dedicated</td>
            <td class="val">{{ $report->male_children_dedicated ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Female children dedicated</td>
            <td class="val">{{ $report->female_children_dedicated ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind">Death</td>
            <td class="sub">(Children)</td>
            <td class="val">{{ $report->deaths_children ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind"></td>
            <td class="sub">(Adults)</td>
            <td class="val">{{ $report->deaths_adults ?: '' }}</td>
        </tr>

        {{-- ══ WORSHIP AND CHURCH ATTENDANCE ═════════════════════ --}}
        <tr class="sec-hdr"><td colspan="4">Worship and Church Attendance</td></tr>

        {{-- Sunday Services --}}
        <tr>
            <td class="cat" rowspan="3">Sunday services</td>
            <td class="ind" colspan="2">Sunday morning attendance</td>
            <td class="val">{{ $report->sunday_morning_attendance ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Communion Sunday attendance</td>
            <td class="val">{{ $report->communion_sunday_attendance ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Communion Participants</td>
            <td class="val">{{ $report->communion_participants ?: '' }}</td>
        </tr>

        {{-- Classes and Teachings --}}
        <tr>
            <td class="cat" rowspan="4">Classes and teachings</td>
            <td class="ind" colspan="2">New Converts Classes held</td>
            <td class="val">{{ $report->new_converts_classes_held ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">New Converts Class attendance</td>
            <td class="val">{{ $report->new_converts_class_attendance ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">New converts retained</td>
            <td class="val">{{ $report->new_converts_retained ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Presiding Elder visits new converts class</td>
            <td class="val">{{ $report->elder_visits_new_converts ?: '' }}</td>
        </tr>

        {{-- Mid-Week Service --}}
        <tr>
            <td class="cat" rowspan="4">Mid-Week Service</td>
            <td class="ind" colspan="2">Mid-Week Church teachings</td>
            <td class="val">{{ $report->midweek_teachings ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Mid-Week Church Attendance</td>
            <td class="val">{{ $report->midweek_attendance ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Weekly Prayer meetings</td>
            <td class="val">{{ $report->weekly_prayer_meetings ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Weekly prayer attendance</td>
            <td class="val">{{ $report->weekly_prayer_attendance ?: '' }}</td>
        </tr>

        {{-- Special Programs --}}
        <tr>
            <td class="cat" rowspan="5">Special Programs</td>
            <td class="ind" colspan="2">Annual Thematic teachings</td>
            <td class="val">{{ $report->annual_thematic_teachings ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Holy Ghost Prayer sessions</td>
            <td class="val">{{ $report->holy_ghost_prayer_sessions ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Marriage teachings</td>
            <td class="val">{{ $report->marriage_teachings ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Blessed marriages</td>
            <td class="val">{{ $report->blessed_marriages ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Intergenerational services</td>
            <td class="val">{{ $report->intergenerational_services ?: '' }}</td>
        </tr>

        {{-- ══ SOCIAL INTERVENTIONS ═══════════════════════════════ --}}
        <tr class="sec-hdr"><td colspan="4">Social Interventions</td></tr>

        {{-- Interventions --}}
        <tr>
            <td class="cat" rowspan="6">Interventions</td>
            <td class="ind" colspan="2">Tertiary sponsorship</td>
            <td class="val">{{ $report->social_tertiary_sponsorship ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Pre-tertiary sponsorship</td>
            <td class="val">{{ $report->social_pre_tertiary_sponsorship ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Health Support</td>
            <td class="val">{{ $report->social_health_support ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Apprenticeship Support</td>
            <td class="val">{{ $report->social_apprenticeship_support ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Community Transformation</td>
            <td class="val">{{ $report->social_community_transformation ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Environmental care</td>
            <td class="val">{{ $report->social_environmental_care ?: '' }}</td>
        </tr>

        {{-- Beneficiaries --}}
        <tr>
            <td class="cat" rowspan="3">Beneficiaries</td>
            <td class="ind" colspan="2">Male Beneficiaries</td>
            <td class="val">{{ $report->beneficiaries_male ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Female beneficiaries</td>
            <td class="val">{{ $report->beneficiaries_female ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Other beneficiaries</td>
            <td class="val">{{ $report->beneficiaries_other ?: '' }}</td>
        </tr>

        {{-- Financial Data --}}
        <tr>
            <td class="cat" rowspan="2">Financial Data</td>
            <td class="ind" colspan="2">Amount Spent on Human Development</td>
            <td class="val">{{ $report->amount_spent_human_development > 0 ? number_format($report->amount_spent_human_development, 2) : '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Amount spent on non-human</td>
            <td class="val">{{ $report->amount_spent_non_human > 0 ? number_format($report->amount_spent_non_human, 2) : '' }}</td>
        </tr>

        {{-- ══ TITHES AND OFFERING ════════════════════════════════ --}}
        <tr class="sec-hdr"><td colspan="4">Tithes and offering</td></tr>

        <tr>
            <td class="cat" rowspan="2"></td>
            <td class="ind" colspan="2">Monthly Net Tithes</td>
            <td class="val">{{ $report->monthly_net_tithes > 0 ? number_format($report->monthly_net_tithes, 2) : '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Monthly Missions offering</td>
            <td class="val">{{ $report->monthly_missions_offering > 0 ? number_format($report->monthly_missions_offering, 2) : '' }}</td>
        </tr>

    </table>

    {{-- ── Notes ────────────────────────────────────────────────── --}}
    @if($report->notes)
    <div style="margin-top:5mm; font-size:9px;">
        <strong>Notes:</strong> {{ $report->notes }}
    </div>
    @endif

    {{-- ── Signature Block ─────────────────────────────────────── --}}
    <div class="sig-area">
        <div class="sig-block">
            <span class="sig-line"></span>
            <div class="sig-label">District Pastor's Signature &amp; Date</div>
        </div>
        <div class="sig-block">
            <span class="sig-line"></span>
            <div class="sig-label">District Overseer's Signature &amp; Date</div>
        </div>
    </div>

    {{-- ── Footer ──────────────────────────────────────────────── --}}
    <div class="doc-footer">
        <span>Prepared by: {{ $report->creator?->name ?? '—' }}</span>
        <span>
            @if($report->status === 'submitted')
                Submitted: {{ $report->submitted_at?->format('d M Y') }} by {{ $report->submitter?->name ?? '—' }}
            @else
                Status: Draft
            @endif
        </span>
        <span>Printed: {{ now()->format('d M Y H:i') }}</span>
    </div>

</div>
</body>
</html>
